<?php
require 'Grain.php';
class Job
{
    var $grain;
    var $key;
    var $iv;
    var $task;
    var $db;
    var $imgname;
    var $imgid;
    var $imgdir;
    var $new_dir;

    public function setUp(){
        $this->key = $this->args['key'];
        $this->iv = $this->args['iv'];
        $this->task = $this->args['task'];
        $this->imgdir = $this->args['dir'];
        $this->imgname = $this->args['name'];
        $this->imgid = $this->args['id'];
        $this->db = new PDO('sqlite:job.db');
        $this->grain = new Grain('80000000000000000000', '0000000000000000');

        // Update status images on sqlite
        $qry = $this->db->prepare('UPDATE Job SET name = ?, task = ?, key = ?, iv = ?, status = ?, dir = ? WHERE id = ?');
        $qry->execute(array($this->imgname, $this->task, $this->key, $this->iv, "On Proccess", $this->imgdir, $this->imgid));
    }

    public function perform(){
        $img = imagecreatefrompng($this->imgdir);
        $x = imagesx($img);
        $y = imagesy($img);

        if($this->task == 'Encrypt'){
            for($i = 0; $i < $x; $i++){
                for($j = 0; $j < $y; $j++){
                    $rgb = imagecolorat($img, $i, $j);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
            
                    $rgb = array($r, $g, $b);
                    $rgb = $this->grain->encrypt($rgb);
                    $new_color = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
                    imagesetpixel($img, $i, $j, $new_color);
                }
                //echo "Proccesing " . $i;
            }
            $this->new_dir = 'assets/images/encrypted/' . $this->imgname;
            imagepng($img, $this->new_dir);
            imagedestroy($img);
        }
        else{
            for($i = 0; $i < $x; $i++){
                for($j = 0; $j < $y; $j++){
                    $rgb = imagecolorat($img, $i, $j);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
            
                    $rgb = array($r, $g, $b);
                    $rgb = $this->grain->decrypt($rgb);
                    $new_color = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
                    imagesetpixel($img, $i, $j, $new_color);
                }
                //echo "Proccesing " . $i;
            }
            $this->new_dir = 'assets/images/decrypted/' . $this->imgname;
            imagepng($img, $this->new_dir);
            imagedestroy($img);
        }
    }

    public function tearDown(){
        // Update db status
        // Update status images on sqlite
        $qry = $this->db->prepare('UPDATE Job SET name = ?, task = ?, key = ?, iv = ?, status = ?, dir = ? WHERE id = ?');
        $qry->execute(array($this->imgname, $this->task, $this->key, $this->iv, "Completed", $this->new_dir, $this->imgid));

        // Remove images in task folder
        unlink($this->imgdir);
    }
}
?>



