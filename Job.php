<?php
require 'Grain.php';
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

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
        // $qry = $this->db->prepare('UPDATE Job SET name = ?, task = ?, key = ?, iv = ?, status = ?, dir = ? WHERE id = ?');
        // $qry->execute(array($this->imgname, $this->task, $this->key, $this->iv, "On Proccess", $this->imgdir, $this->imgid));
    }

    public function perform(){
        $img = imagecreatefrompng($this->imgdir);
        $x = imagesx($img);
        $y = imagesy($img);
        $total = ($x) * ($y);
        $progress = 0;
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

                $progress = ceil(((($i+1) * ($y)) / $total) * 100);
                $sse = array('id' => $this->imgid, 'progress' => $progress);

                // Update status images on sqlite
                $qry = $this->db->prepare('UPDATE Job SET status = ? WHERE id = ?');
                $qry->execute(array((string)$progress, $this->imgid));

                $fp = fopen('progress.json', 'w');
                fwrite($fp, json_encode($sse));
                fclose($fp);
                //echo "Proccesing " . $i;
            }
            $this->new_dir = 'assets/images/encrypted/' . time() . $this->imgname;
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
                $progress = ceil(((($i+1) * ($y)) / $total) * 100);
                $sse = array('id' => $this->imgid, 'progress' => $progress);

                // Update status images on sqlite
                $qry = $this->db->prepare('UPDATE Job SET status = ? WHERE id = ?');
                $qry->execute(array((string)$progress, $this->imgid));

                $fp = fopen('progress.json', 'w');
                fwrite($fp, json_encode($sse));
                fclose($fp);
            }
            $this->new_dir = 'assets/images/decrypted/' . time() . $this->imgname;
            imagepng($img, $this->new_dir);
            imagedestroy($img);
        }
    }

    public function tearDown(){
        // Update db status
        // Update status images on sqlite
        $qry = $this->db->prepare('UPDATE Job SET name = ?, task = ?, key = ?, iv = ?, status = ?, dir = ? WHERE id = ?');
        $qry->execute(array($this->imgname, $this->task, $this->key, $this->iv, "100", $this->new_dir, $this->imgid));

        // Remove images in task folder
        unlink($this->imgdir);
    }
}

if(isset($_GET['id'])){
    $request = $_GET['id'];
    $json = file_get_contents('./progress.json');
    $json_data = json_decode($json, true);
    if($json_data['id'] == $request){
        $sse = array('id' => $json_data['id'], 'progress' => $json_data['progress']);
        echo "data: ". json_encode($sse). "\n\n";
        flush();
    }
    else{
        $sse = array('id' => null, 'progress' => null);
        echo "data: ". json_encode($sse). "\n\n";
        flush();
    }
}
?>



