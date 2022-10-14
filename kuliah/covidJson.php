<?php
$url = 'https://data.covid19.go.id/public/api/prov.json';
$data1 = file_get_contents($url);
$data_covid = json_decode($data1);

echo "<pre>"; print_r($data_covid->list_data);

$table = "<h1>Data Covid</h1>";
$table .= "<table border = 1>";
$table .= "<tr><td>No</td>
				<td>Provinsi</td>
				<td>Sembuh</td>
				<td>Meninggal</td>
				<td>Dirawat</td>";

for ($i = 0; $i <count($data_covid->list_data); $i++){
	$no = $i+1;
	$table .= "<tr>
				<td>{$no}</td>
				<td>{$data_covid->list_data[$i]->key}</td>
				<td>{$data_covid->list_data[$i]->jumlah_sembuh}</td>
				<td>{$data_covid->list_data[$i]->jumlah_meninggal}</td>
				<td>{$data_covid->list_data[$i]->jumlah_dirawat}</td>
				</tr>";
}
$table .="</table>";
echo $table;

?>