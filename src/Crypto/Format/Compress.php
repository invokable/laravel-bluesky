<?php

declare(strict_types=1);

namespace Revolution\Bluesky\Crypto\Format;

use Illuminate\Support\Str;
use phpseclib3\Crypt\EC\BaseCurves\Base;
use phpseclib3\Math\PrimeField\Integer as PrimeInteger;

/**
 * phpseclib custom key format.
 *
 * Save as compressed public key.
 */
final class Compress
{
    /**
     * @param  array{0: PrimeInteger, 1: PrimeInteger}  $publicKey
     */
    public static function savePublicKey(Base $curve, array $publicKey, array $options = []): string
    {
        $prefix = $publicKey[1]->isOdd() ? '03' : '02';

        // 32
        $length = $curve->getLengthInBytes();

        $hexString = Str::padLeft($publicKey[0]->toHex(), $length, pad: '0');

        return $prefix.$hexString;
    }
}
