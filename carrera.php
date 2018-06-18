
<?php

  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect('localhost','root','');

  if (mysqli_connect_errno()) echo "Error al conectar con las base de datos: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, 'easymoto');

?>


<!-- Display table data. -->
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>id</td>
    <td>inicio</td>
    <td>fin</td>
    <td>opcion</td>
  </tr>

<?php

$result = mysqli_query($connection, " select id,direccion_inicio,direccion_fin,opciones from carrera where id_pedido=3;
"); 

while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>",$query_data[0], "</td>",
       "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$query_data[3], "</td>";
  echo "</tr>";
}
?>

</table>

<!-- Clean up. -->
<?php
  mysqli_close($connection);

?>

</body>
</html>


