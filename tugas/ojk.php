<?php
$data=file_get_contents("https://ojk-invest-api.vercel.app/api/illegals");
$ojk=json_decode($data, true);
//echo "<pre>";print_r($ojk);
$table = "<h3>Data-data Perusahaan OJK</h3>";
$table .= "<table border=1>
			<tr><td>No</td>
			    <td>Nama PT</td>
				<td>Alamat</td>
				<td>Telephone</td>
				<td>Tipe</td>
				<td>Web</td></tr>";

for($i=0;$i<count($ojk["data"]["illegals"]);$i++){

	//cek dulu apakah array number kosong atau tidak
		if (empty($ojk["data"]["illegals"][$i]["number"])){
			$display_number = null;
		}else 
		for($p=0; $p<count($ojk["data"]["illegals"][$i]["number"]);$p++){
		$display_number = $ojk["data"]["illegals"][$i]["number"][$p];
		}
	//cek dulu apakah array urls kosong atau tidak
		if (empty($ojk["data"]["illegals"][$i]["urls"])){
			$display_urls = null;
		}else 
		for($z=0; $z<count($ojk["data"]["illegals"][$i]["urls"]);$z++){
		$display_urls = $ojk["data"]["illegals"][$i]["urls"][$z];
		}

	$no=$i+1;
	$table .= "<tr><td>{$no}</td>
			    <td>{$ojk["data"]["illegals"][$i]["name"]}</td>
			    <td>{$ojk["data"]["illegals"][$i]["address"]}</td>
			    <td>{$display_number}</td>
			    <td>{$ojk["data"]["illegals"][$i]["type"]}</td>
			    <td>{$display_urls}</td>
				</tr>";
}
$table .= "</table>";
echo $table;
?>