<?php
class Grain{
    var $NFSR;
    var $LFSR;
    var $keysize = 80;
    var $ivsize = 64;
    var $NFTable = array(
        0,1,0,0,1,0,1,1,1,0,1,1,0,1,0,0,1,0,1,1,0,1,0,0,0,1,0,0,0,1,0,1,
        1,0,1,1,0,1,0,0,0,1,0,0,1,0,1,1,1,0,1,1,0,1,0,0,0,1,0,0,0,1,0,1,
        1,0,1,1,0,1,0,0,0,1,0,0,1,0,1,1,0,1,0,0,1,0,1,1,1,0,1,1,1,0,1,0,
        0,1,0,0,1,0,1,1,1,0,1,1,0,1,0,0,0,1,0,0,1,0,1,1,1,0,1,1,1,0,1,0,
        1,0,1,1,0,1,0,0,0,1,0,0,1,0,1,1,0,1,0,0,1,0,1,1,1,0,1,1,1,0,1,0,
        0,1,0,0,1,0,1,1,1,0,1,1,0,1,0,0,0,1,0,0,1,0,1,1,1,0,1,1,1,0,1,0,
        0,1,0,0,1,0,1,1,1,0,1,1,0,1,0,0,1,0,1,1,0,1,0,0,0,1,0,0,0,1,0,1,
        1,0,1,1,0,1,0,0,0,1,0,0,1,0,1,1,1,0,1,1,0,1,0,0,0,1,0,0,1,0,1,0,
        1,0,1,1,0,1,0,0,0,1,0,0,1,0,1,1,0,1,0,0,1,0,1,1,1,0,1,1,1,0,1,0,
        0,1,0,0,1,0,1,1,1,0,1,1,0,1,0,0,0,1,0,0,1,0,1,1,1,0,1,1,1,0,1,0,
        0,1,0,0,1,0,1,1,1,0,1,1,0,1,0,0,1,0,1,1,0,1,0,0,0,1,0,0,0,1,0,1,
        1,0,1,1,0,1,0,0,0,1,0,0,1,0,1,1,1,0,1,1,0,1,0,0,0,1,0,0,0,1,0,1,
        0,1,0,0,1,0,1,1,1,0,1,1,0,1,0,0,1,0,1,1,0,1,0,0,0,1,0,0,0,1,0,1,
        1,0,1,1,0,1,0,0,0,1,0,0,1,0,1,1,0,1,0,0,1,0,1,1,1,0,1,1,1,0,1,0,
        0,1,0,0,1,0,1,1,1,0,1,1,0,1,0,0,1,0,1,1,0,1,0,0,0,1,0,0,0,1,0,1,
        1,0,1,1,0,1,0,0,0,1,0,0,1,0,1,1,0,1,0,0,1,0,1,1,1,0,1,1,0,1,0,1,
        0,1,0,0,1,0,1,1,1,0,1,1,0,1,0,0,1,0,1,1,0,1,0,0,0,1,0,0,0,1,0,1,
        1,0,1,1,0,1,0,0,0,1,0,0,1,0,1,1,1,0,1,1,0,1,0,0,0,1,0,0,0,1,0,1,
        1,0,1,1,0,1,0,0,0,0,0,1,1,1,1,0,0,1,0,0,1,0,1,1,1,1,1,0,1,1,1,1,
        0,1,0,0,1,0,1,1,1,1,1,0,0,0,0,1,0,1,0,0,1,0,1,1,1,1,1,0,1,1,1,1,
        1,0,1,1,0,1,0,0,0,1,0,0,1,0,1,1,0,1,0,0,1,0,1,1,1,0,1,1,1,0,1,0,
        0,1,0,0,1,0,1,1,1,0,1,1,0,1,0,0,0,1,0,0,1,0,1,1,1,0,1,1,1,0,1,0,
        0,1,0,0,1,0,1,1,1,1,1,0,0,0,0,1,1,0,1,1,0,1,0,0,0,0,0,1,0,0,0,0,
        1,0,1,1,0,1,0,0,0,0,0,1,1,1,1,0,1,0,1,1,0,1,0,0,0,0,0,1,1,1,1,1,
        0,1,0,0,1,0,0,0,1,0,1,1,0,1,1,1,1,0,1,1,0,1,1,1,0,1,0,0,0,1,1,0,
        1,0,1,1,0,1,1,1,0,1,0,0,1,0,0,0,1,0,1,1,0,1,1,1,0,1,0,0,0,1,1,0,
        1,0,1,1,0,1,1,1,0,0,0,1,1,1,0,1,0,1,0,0,1,0,0,0,1,1,1,0,1,1,0,0,
        0,1,0,0,1,0,0,0,1,1,1,0,0,0,1,0,0,1,0,0,1,0,0,0,1,1,1,0,1,1,0,0,
        1,0,1,1,0,1,1,1,0,1,0,0,1,0,0,0,0,1,0,0,1,0,0,0,1,0,1,1,1,0,0,1,
        0,1,0,0,1,0,0,0,1,0,1,1,0,1,1,1,1,0,1,1,0,1,1,1,0,1,0,0,0,1,1,0,
        1,0,1,1,0,1,1,1,0,0,0,1,1,1,0,1,0,1,0,0,1,0,0,0,1,1,1,0,1,1,0,0,
        1,0,1,1,0,1,1,1,0,0,0,1,1,1,0,1,0,1,0,0,1,0,0,0,1,1,1,0,0,0,1,1
    );
    var $boolTable = array(0,0,1,1,0,0,1,0,0,1,1,0,1,1,0,1,1,1,0,0,1,0,1,1,0,1,1,0,0,1,0,0);

    function __construct($key, $iv){
        // Convert string key  and iv of hex 0000000 to array(key in int)
        $inthex = function($value){
            return hexdec($value);
        };

        $key = str_split($key, 2);
        $key = array_map($inthex,$key);

        $iv = str_split($iv, 2);
        $iv = array_map($inthex,$iv);

        $this->NFSR = array_fill(0, 80, 0);
        $this->LFSR = array_fill(0, 80, 0);

        //print_r($this->NFSR);

        // key and iv setup load register
        for($i=0; $i < $this->ivsize / 8; $i++){
            for($j=0; $j < 8; $j++){
                $this->NFSR[$i * 8 + $j] = ($key[$i] >> $j) & 1;
                $this->LFSR[$i * 8 + $j] = ($iv[$i] >> $j) & 1;
            }
        }

        for($i=$this->ivsize/8; $i < $this->keysize / 8; $i++){
            for($j=0; $j < 8; $j++){
                $this->NFSR[$i * 8 + $j] = ($key[$i] >> $j) & 1;
                $this->LFSR[$i * 8 + $j] = 1;
            }
        }

        // Initial Clockint
        for($i=0; $i < 160; $i++){
            $outbit = $this->keystream();
            $this->LFSR[79] ^= $outbit;
            $this->NFSR[79] ^= $outbit;
        }
        //print_r($this->NFSR);
    }

    private function N($i){
        return $this->NFSR[80 - $i];
    }
    private function L($i){
        return $this->LFSR[80 - $i];
    }
    public function keystream(){
        $X0 = $this->LFSR[3];
        $X1 = $this->LFSR[25];
        $X2 = $this->LFSR[46];
        $X3 = $this->LFSR[64];
        $X4 = $this->NFSR[63];

        // Calculate feedback and output bits
        $outbit = $this->N(79)^$this->N(78)^$this->N(76)^$this->N(70)^$this->N(49)^$this->N(37)^$this->N(24)^$this->boolTable[($X4<<4) | ($X3<<3) | ($X2<<2) | ($X1<<1) | $X0];
        $NBit = $this->L(80)^$this->N(18)^$this->N(66)^$this->N(80)^$this->NFTable[($this->N(17)<<9) | ($this->N(20)<<8) | ($this->N(28)<<7) | ($this->N(35)<<6) | ($this->N(43)<<5) | ($this->N(47)<<4) | ($this->N(52)<<3) | ($this->N(59)<<2) | ($this->N(65)<<1) | $this->N(71)];
        $LBit = $this->L(18)^$this->L(29)^$this->L(42)^$this->L(57)^$this->L(67)^$this->L(80);

        // Update register
        for($i=1; $i < $this->keysize; $i++){
            $this->NFSR[$i-1] = $this->NFSR[$i];
            $this->LFSR[$i-1] = $this->LFSR[$i];
        }

        $this->NFSR[$this->keysize - 1] = $NBit;
        $this->LFSR[$this->keysize - 1] = $LBit;

        return $outbit;
    }

    public function keystream_bytes($msglen){
        $keystream = array_fill(0, $msglen, 0);

        for($i=0; $i < $msglen; $i++){
            for($j=0; $j < 8; $j++){
                $outbit = $this->keystream();
                $outbit <<= $j;
                $keystream[$i] = $keystream[$i] | $outbit;
            }
        }

        return $keystream;
    }
    public function encrypt($msg){
        $keystream = $this->keystream_bytes(sizeof($msg));
        $cipher = array();
        
        for($i=0; $i<sizeof($msg); $i++){
            $cipher[] = $keystream[$i] ^ $msg[$i];
        }

        return $cipher;
    }
    public function decrypt($msg){
        $keystream = $this->keystream_bytes(sizeof($msg));
        $plain = array();
        
        for($i=0; $i<sizeof($msg); $i++){
            $plain[] = $keystream[$i] ^ $msg[$i];
        }

        return $plain;
    }
}


//$grain = new Grain('80000000000000000000', '0000000000000000');
//print_r($grain->encrypt(array(1,2,3)));
?>