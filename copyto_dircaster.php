<?php
echo "// Copy and paste this information ABOVE the main code section of dircaster.<br>";
echo "// replacing all of the default settings in dircaster.<br>";
echo "// <br>";
echo "// dircaster specific variables<br>";
write_item ($feeds, "maxFeeds");
write_item ($sft, "sftypes");
write_item ($id3_lib, "id3LibPath");
echo "<p></p>";

echo "// RSS general tags<br>";
write_item ($title, "titleTAG");
write_item ($desc, "descriptionTAG");
write_item ($link, "linkTAG");
write_item ($copyr, "copyrightTAG");
write_item ($lang, "languageTAG");
write_item ($email, "webMasterTAG");
write_item ($gen, "generatorTAG");
write_item ($ttl, "ttlTAG");
write_item ($rss_img_url, "rssImageUrlTAG");
write_item ($rss_img_title, "rssImageTitleTAG");
write_item ($rss_img_link, "rssImageLinkTAG");
echo "<p></p>";

echo "// iTunes specific tags<br>";
write_item ($xmlns, "nameSpaceTAG");
if ($explicit == "yes" ) {
	write_item ($explicit, "explicitTAG");
	} else {
	write_item ("no", "explicitTAG");
}
write_item ($sum, "summaryTAG");
write_item ($author, "authorTAG");
write_item ($owner_name, "ownerNameTAG");
write_item ($owner_email, "ownerEmailTAG");
write_item ($top_cat, "topCategoryTAG");
write_item ($sub_cat, "subCategoryTAG");
write_item ($key, "keywordTAG");
write_item ($img_url, "imageUrlTAG");
write_item ($img_title, "imageTitleTAG");
write_item ($img_link, "imageLinkTAG");
if ($img_item == "yes" ) {
	write_item ($img_item, "imageItemTAG");
	} else {
	write_item ("no", "imageItemTAG");
}
echo "// <br>";
echo "// end of dircaster variables.";

// functions

function write_item ($input, $desc) {
	$fixed = str_replace("&","&#38;", $input);
	$fixed = str_replace("<","&#60;",$fixed);
	$fixed = str_replace(">","&#62;",$fixed);
	$line = "\$".$desc."=\"".$fixed."\";";
	echo $line."<br>";
}

?>