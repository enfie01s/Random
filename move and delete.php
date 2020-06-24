<?php
move($target_path2a . $id2,"TileGroup0",$target_path2a . $id2);//move TileGroup0 to products/id folder (instead of sub directory from the zip
$allowed = array("/TileGroup0","/ImageProperties.xml");//items to keep
$contents = glob($target_path2a.$id2."/*");//get contents of products/id folder
foreach($contents as $item)
{
	if(!in_array(strrchr($item,"/"),$allowed))//ignore the items we want in the folder
	{
		$item_to_delete=$target_path2a.$id2.strrchr($item,"/");
		if(is_dir($item_to_delete)){echo($item_to_delete."/");}//remove all unwanted folders
		else{echo($item_to_delete);}//remove all unwanted files
	}
}	
$stop = 0;
function move($tdir,$search,$tolocation)
{
	global $stop, $location, $target_path2a;
	if ($handle = opendir($tdir)) 
	{
    while (false !== ($file = readdir($handle))) 
		{
			 if($file != ".."&&$file != "."&&is_dir($tdir."/".$file)&&$file!=$search){
			 		move($tdir."/".$file,$search,$tolocation);
			 }
			 else if($file != ".."&&$file != "."&&is_dir($tdir."/".$file)&&$file==$search)
			 {
				 if($stop == 0 && $tdir != $tolocation)
				 {
						if(file_exists($tolocation."/".$file."/")){rmdirr($tolocation."/".$file."/");} 
						if(file_exists($tolocation."/ImageProperties.xml")){unlink($tolocation."/ImageProperties.xml");}
						rename($tdir."/".$file,$tolocation."/".$file);
						rename($tdir."/ImageProperties.xml",$tolocation."/ImageProperties.xml");	
						$stop = 1;
				}
			}
    }
    closedir($handle);
	}
}
function unzipZoomify($thezip,$targetdir)
{
	require "../content/zip.class.php"; // Get the zipfile class
	$tiledirtarget=$targetdir."/TileGroup0/";
	$imgpropstarget=$targetdir."/ImageProperties.xml";
	if(file_exists($targetdir)){rmdirr($targetdir."/");} //remove old images
	mkdir($targetdir);
	mkdir($tiledirtarget);
	$zipfile = new zipfile; // Create an object
	$zipfile->read_zip($zipfile); // Read the zip file			
	foreach($zipfile->files as $filea)
	{
		if($filea['name'] == "ImageProperties.xml" || strrchr($filea['dir'],"TileGroup0") == "TileGroup0")//ignore superfluous files
		{
			$putlocale=($filea['name']=="ImageProperties.xml")?$targetdir."/".$filea['name']:$targetdir."/TileGroup0/".$filea['name'];
			file_put_contents($putlocale,$filea['data']);
			$outcome = "success";
		}
	}
	if(!isset($outcome)){}
}
function rmdirr($dirname)  
{
	//$dir = $dirname;
	if(is_dir($dirname))
	{	
		$files = glob($dirname."*");
		foreach($files as $file)
		{
			if(is_dir($file)){rmdirr($file."/");}else{unlink($file);}
		} 
		echo($dirname);
	}
}
?>