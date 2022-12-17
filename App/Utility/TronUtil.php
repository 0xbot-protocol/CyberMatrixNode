<?php

namespace  App\Utility;
use BitWasp\Bitcoin\Crypto\Random\Random;
use BitWasp\Bitcoin\Key\Factory\HierarchicalKeyFactory;
use BitWasp\Bitcoin\Mnemonic\Bip39\Bip39Mnemonic;
use BitWasp\Bitcoin\Mnemonic\Bip39\Bip39SeedGenerator;
use BitWasp\Bitcoin\Mnemonic\MnemonicFactory;
use BitWasp\Buffertools\Buffer;
use EasySwoole\Component\Singleton;
use Elliptic\EC;
use IEXBase\TronAPI\Support\Keccak;
use IEXBase\TronAPI\Support\Base58;
use IEXBase\TronAPI\Support\Crypto;
use IEXBase\TronAPI\Support\Hash;

class TronUtil
{
    const ADDRESS_SIZE = 34;
    const ADDRESS_PREFIX = "41";
    const ADDRESS_PREFIX_BYTE = 0x41;

    use Singleton;

    /**
     * Default Address
     *
     * @var array
     */
    public $address = [
        'base58'    =>  null,
        'hex'       =>  null
    ];

    public function getBase58CheckAddress(string $addressBin): string
    {
        $hash0 = Hash::SHA256($addressBin);
        $hash1 = Hash::SHA256($hash0);
        $checksum = substr($hash1, 0, 4);
        $checksum = $addressBin . $checksum;

        return Base58::encode(Crypto::bin2bc($checksum));
    }

    public function getAddressHex(string $pubKeyBin): string
    {
        if (strlen($pubKeyBin) == 65) {
            $pubKeyBin = substr($pubKeyBin, 1);
        }

        $hash = Keccak::hash($pubKeyBin, 256);

        return self::ADDRESS_PREFIX . substr($hash, 24);
    }


    public function getSeed($mnemonic="") {
        if(empty($mnemonic)) {
            $random = new  Random();
            // 生成随机数(initial entropy)
            $entropy = $random->bytes(Bip39Mnemonic::MIN_ENTROPY_BYTE_LEN);
            $bip39 = MnemonicFactory::bip39();
            // 通过随机数生成助记词
            $mnemonic = $bip39->entropyToMnemonic($entropy);
        }
        $seedGenerator = new Bip39SeedGenerator();
//        $seed = $seedGenerator->getSeed($mnemonic, 'Vi9aj.kbef');
        $seed = $seedGenerator->getSeed($mnemonic);

        return [
            'seed'=>$seed,
            'mnemonic'=>$mnemonic
        ];


    }
    public function getMnemonic() {
        $random = new  Random();
        // 生成随机数(initial entropy)

        $entropy = $random->bytes(Bip39Mnemonic::MIN_ENTROPY_BYTE_LEN);

        $bip39 = MnemonicFactory::bip39();
        // 通过随机数生成助记词
        $mnemonic = $bip39->entropyToMnemonic($entropy);
        return $mnemonic;
    }


    public function getTrxAddres($mnemonic,$i=0) {
//        $seedMeta = $this->getSeed($mnemonic);
//       // print_r($seedMeta);
//        $seed = $seedMeta['seed'];
//        echo $seedMeta['mnemonic'];

        $seedGenerator = new Bip39SeedGenerator();
//        $seed = $seedGenerator->getSeed($mnemonic, 'Vi9aj.kbef');
        $seed = $seedGenerator->getSeed($mnemonic);


        $hdFactory = new HierarchicalKeyFactory();
        $master = $hdFactory->fromEntropy($seed);
        $hardened = $master->derivePath("44'/195'/{$i}'/0/0");
        //  const child = node.derivePath(`m/44'/195'/${ index }'/0/0`);


        // keyFromPrivate

        $ec = new EC('secp256k1');

        $pubKeyHex1 = $hardened->getPublicKey()->getHex();
        $privteakey = $hardened->getPrivateKey()->getHex();
        //  echo "raw prv 1:".$privteakey."\n";
        $priv = $ec->keyFromPrivate($privteakey);
        //echo "raw prv 2:".$priv->getPrivate('hex')."\n";

        $pubKeyHex = $priv->getPublic(false, "hex");
        // echo "raw pub 1:".$pubKeyHex1."\n";
        //echo "raw pub 2:".$pubKeyHex."\n";

        $pubKeyBin = hex2bin($pubKeyHex);
        $addressHex = $this->getAddressHex($pubKeyBin);
        $addressBin = hex2bin($addressHex);
        $addressBase58 = $this->getBase58CheckAddress($addressBin);


        $address = [
            'private_key' => $privteakey,
            'public_key'    => $pubKeyHex,
            'address_hex' => $addressHex,
            'address_base58' => $addressBase58
        ];
        return $address;

    }




}


