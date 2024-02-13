<?php

namespace App\Http\Services\Recorrido;

use Exception;

class PolylineDecoderService {
    const DEFAULT_PRECISION = 5;
    const ENCODING_TABLE = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_";
    const DECODING_TABLE = [
        62, -1, -1, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1, -1,
        0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21,
        22, 23, 24, 25, -1, -1, -1, -1, 63, -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35,
        36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51
    ];
    const FORMAT_VERSION = 1;
    const ABSENT = 0;
    const LEVEL = 1;
    const ALTITUDE = 2;
    const ELEVATION = 3;
    const CUSTOM1 = 6;
    const CUSTOM2 = 7;

    public static function decodeChar($char) {
        $charCode = ord($char);
        return self::DECODING_TABLE[$charCode - 45];
    }

    public static function decodeUnsignedValues($encoded) {
        $result = 0;
        $shift = 0;
        $resList = [];

        foreach (str_split($encoded) as $char) {
            $value = self::decodeChar($char);
            $result |= ($value & 0x1F) << $shift;
            if (($value & 0x20) === 0) {
                $resList[] = $result;
                $result = 0;
                $shift = 0;
            } else {
                $shift += 5;
            }
        }

        if ($shift > 0) {
            throw new Exception('Invalid encoding');
        }

        return $resList;
    }

    public static function decodeHeader($version, $encodedHeader) {
        if ((int)$version !== self::FORMAT_VERSION) {
            throw new Exception('Invalid format version');
        }
        $headerNumber = (int)$encodedHeader;
        $precision = $headerNumber & 15;
        $thirdDim = ($headerNumber >> 4) & 7;
        $thirdDimPrecision = ($headerNumber >> 7) & 15;
        return ['precision' => $precision, 'thirdDim' => $thirdDim, 'thirdDimPrecision' => $thirdDimPrecision];
    }

    public static function toSigned($val) {
        $res = $val;
        if ($res & 1) {
            $res = ~$res;
        }
        $res >>= 1;
        return (int)$res;
    }

    public static function decode($encoded) {
        $decoder = self::decodeUnsignedValues($encoded);
        $header = self::decodeHeader($decoder[0], $decoder[1]);

        $factorDegree = 10 ** $header['precision'];
        $factorZ = 10 ** $header['thirdDimPrecision'];
        $thirdDim = $header['thirdDim'];

        $lastLat = 0;
        $lastLng = 0;
        $lastZ = 0;
        $res = [];

        $i = 2;
        while ($i < count($decoder)) {
            $deltaLat = self::toSigned($decoder[$i]) / $factorDegree;
            $deltaLng = self::toSigned($decoder[$i + 1]) / $factorDegree;
            $lastLat += $deltaLat;
            $lastLng += $deltaLng;

            if ($thirdDim) {
                $deltaZ = self::toSigned($decoder[$i + 2]) / $factorZ;
                $lastZ += $deltaZ;
                $res[] = [$lastLat, $lastLng, $lastZ];
                $i += 3;
            } else {
                $res[] = [$lastLat, $lastLng];
                $i += 2;
            }
        }

        if ($i !== count($decoder)) {
            throw new Exception('Invalid encoding. Premature ending reached');
        }

        return [
            'precision' => $header['precision'],
            'thirdDim' => $header['thirdDim'],
            'thirdDimPrecision' => $header['thirdDimPrecision'],
            'polyline' => $res
        ];
    }

    public static function encodeSignedValue($value) {
        $sign = $value < 0 ? 1 : 0;
        $value = ($value << 1) ^ ($value >> 31);
        $encoded = '';
        while ($value >= 0x20) {
            $encoded .= self::ENCODING_TABLE[($value & 0x1F) | 0x20];
            $value >>= 5;
        }
        return $encoded . self::ENCODING_TABLE[$value | $sign << 5];
    }

    public static function encode($polyline) {
        $encoded = '';
        $prevLat = 0;
        $prevLng = 0;
        foreach ($polyline as $point) {
            $lat = $point[0];
            $lng = $point[1];
            $encoded .= self::encodeSignedValue(($lat - $prevLat) * (10 ** self::DEFAULT_PRECISION));
            $encoded .= self::encodeSignedValue(($lng - $prevLng) * (10 ** self::DEFAULT_PRECISION));
            $prevLat = $lat;
            $prevLng = $lng;
        }
        return $encoded;
    }
    
}