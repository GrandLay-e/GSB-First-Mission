<div class ="erreur">
<ul>
<?php 
foreach($_REQUEST['erreurs'] as $erreur)
	{
      echo "<li color = 'red'>$erreur</li>";
	}
?>
</ul></div>
