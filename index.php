<?php
 
require_once(dirname(__FILE__) .'/Uploader.class.php');

use Startimes\Aloza\Uploader;
 
if(isset($_POST['go'])) {
    $configs = array(
        'input_name'         => 'file',
        'location_dir'       => 'uploads',
        'allowed_extensions' => ['png', 'jpg', 'gif', 'zip'],
        'file_size'          => 10240
    );
    $message = (new Uploader($configs))->uploadFile();

    if (is_array($message)) {
    	echo implode('<br />', $message);
    } else {
    	echo $message;
    }
}

?>
 
<!DOCTYPE html>
<html>
<body>
 
<form action="" method="post" enctype="multipart/form-data">
    Select a file to upload:
    <input type="file" name="file">
    <input type="submit" value="Upload File" name="go">
</form>
 
</body>
</html>