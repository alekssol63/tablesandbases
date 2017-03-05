<?php

 if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
$user = "solopov";
$pass = "neto0794";
//$user = "root";
//$pass = "";
$show_table = false;

try {
	$pdo = new PDO('mysql:host=localhost;dbname=solopov;charset=utf8', $user, $pass);//+	
	}catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
if(!(empty($_POST['insertfield']))){
	var_dump($_POST);
	$maintablename=$_POST['maintablename'];
	
	$fname=$_POST['fname'];
	
	$ftype=$_POST['ftype'];
	
	$flength=$_POST['flength'];
	
	$prep_q = $pdo->prepare("ALTER TABLE $maintablename ADD $fname $ftype($flength)");	
	$prep_q->execute(array($maintablename,$fname,$ftype,$flength));
}
if(!(empty($_POST['save']))){
	var_dump($_POST);
	$maintablename=$_POST['maintablename'];
	$mainfieldname=$_POST['mainfieldname'];
	$mainfieldtype=$_POST['mainfieldtype'];
	$mainfieldlength=$_POST['mainfieldlength'];
	$prep_q = $pdo->prepare("CREATE TABLE $maintablename (
	$mainfieldname $mainfieldtype($mainfieldlength) 
	) ENGINE=InnoDB DEFAULT CHARSET=utf8");	
	$prep_q->execute(array($maintablename,$mainfieldname,$mainfieldtype,$mainfieldlength));

}
if (!(empty($_POST['show']))){
	
	$tablename=$_POST['show'];
	$_SESSION['tablename']=$tablename;
	$table_info = $pdo->prepare("DESCRIBE $tablename");
	$table_info->execute(array($tablename));
	
	$t_inf=$pdo->prepare("DESCRIBE $tablename");
	$t_inf->execute(array($tablename));
	$show_table = true;
	

}

if (isset($_POST['selectfield'])){
	$currentfieldnum=$_POST['selectfield'];//
	$tablename=$_SESSION['tablename'];
	
	$t_inf=$pdo->prepare("DESCRIBE $tablename");
	$t_inf->execute(array($tablename));
	$row=$t_inf ->fetchall(PDO::FETCH_ASSOC);
	
	
	$_SESSION['fieldname']=$row[$currentfieldnum]['Field'];
	$_SESSION['fieldtype']=$row[$currentfieldnum]['Type'];

}
if (!(empty($_POST['changefield']))){
	
	$tablename=$_SESSION['tablename'];
	$oldfield=$_SESSION['fieldname'];
	$newfield=$_POST['fieldname'];
	$fieldtype = $_SESSION['fieldtype'];	
	if ($_SESSION['fieldname']!==$_POST['fieldname']){
		$prep_q = $pdo->prepare("ALTER TABLE $tablename CHANGE $oldfield $newfield $fieldtype");	
		$prep_q->execute(array($tablename,$oldfield, $newfield,$fieldtype));
		
	}

		
}



if (!(empty($_POST['deletefield']))){
	$tablename=$_SESSION['tablename'];
	$field=$_POST['fieldname'];
	
	$prep_q = $pdo->prepare("ALTER TABLE $tablename DROP COLUMN $field");	
		$prep_q->execute(array($tablename,$field));
		
}

	if (empty($_SESSION['tablename'])){
		$tablename='';
	}else{
		$tablename=$_SESSION['tablename'];
	}	
	$table_info = $pdo->prepare("DESCRIBE $tablename");
	$table_info->execute(array($tablename));
	
	$t_inf=$pdo->prepare("DESCRIBE $tablename");
	$t_inf->execute(array($tablename));
	$show_table = true;

?>
<html>
<head>


<style>
	table {
    border-collapse: collapse;
	
	}
	th{
	background: gray;
	}
	tr, td, th{
    border: 1px solid black;
	}
</style>  

</head>
<table>
<tr>
<td>
<h2>Создать таблицу</h2>
<p>Для создания таблицы необходимо указать название таблицы, название поля и длину </p>
<form action="" method="post" id="form">
	<p> Название таблицы:
	<input name="maintablename" maxlength="64" value="<?php echo $tablename; ?>">
	<select name="Engine">
		<option value="">(тип)</option>
		<option>InnoDB</option>
	</select>
	

	<p>
	<table >
		<tr>
			<th>Название поля</th>
			<td>Тип</td>
			<td>Длина</td>
			<td>NULL</td>
			<td>Автоматическое приращение</td>
			
		</tr>		
		<tr>
			<th><input name="mainfieldname" value=""></th>
			<td>
				<select name="mainfieldtype">
					<option>tinyint</option>
					<option>smallint</option>
					<option>mediumint</option>
					<option selected="">int</option>
					<option>bigint</option>
					<option>decimal</option>
					<option>float</option>
					<option>double</option>
					<option>date</option>
					<option>datetime</option>
					<option>char</option>
					<option>text</option>
				</select>
			</td>
			<td><input name="mainfieldlength" value=""></td>
			<td><input type="checkbox" name="fieldnull" value="1"></td>
			
			
			<td><input type="checkbox" name="autoinc" value="1"></td>
			<td><button type="submit" name="save" value="save">Сохранить</button></td>
		</tr>
	</table>
		
	</p>
</td>	
</tr>	
</table>	
	<table>
	
	
		<tr>			
			<th>Список таблиц</th>
		</tr>
		
		<?php $prep_q = $pdo->query('SHOW TABLES');
			while ($tables=$prep_q ->fetch(PDO::FETCH_ASSOC)){ ?>	
			
		<tr>
			<?php foreach($tables as $key=>$value){ ?> 
				<td><?php echo $value; ?><td>
				<td style="border-style: none"><button type="submit" name="show" value="<?php echo $value ?>">Показать</button><td>
			<?php } ?>
			
		</tr>	
		<?php } ?>	
	</table>

	
	<?php if ($show_table){ ?>
	<h3><?php echo "Таблица ". $tablename; ?><h3>
		<table>
			
			<tr>
				<th>Field</th>
				<th>Type</th>
				<th>Null</th>
				<th>Key</th>
				<th>Default</th>
				<th>Extra</th>
			</tr>
			<?php while ($row=$table_info ->fetchall(PDO::FETCH_ASSOC)){ ?>
			
			
				<?php foreach($row as $key=>$value){ ?>
				<tr>
				<?php foreach	($value as $k=>$v){ ?>
				<td><?php echo $v; ?></td>
				
				<?php } ?>
				<td><button type="submit" name="selectfield" value="<?php echo $key ?>">Выбрать</button></td>
				<?php } ?>
				
			
				
				
				
			</tr>
			<?php } ?>
		</table>
	<?php }; ?>
	
	

	<table >
		<tr>
			<th>Название поля</th>
			<td>Тип</td>
			<td>Длина</td>
			<td>NULL</td>
			
		</tr>	
	
	 
		<?php
		if (isset($currentfieldnum)){
		$row=$t_inf ->fetchall(PDO::FETCH_ASSOC);
		?>
		<tr>
		
			<td><input name="fieldname" value="<?php echo $row[$currentfieldnum]["Field"]; ?>"></td>
			<td>
				<select name="fieldtype">
				<?php 
				$selected=NULL;
					$option_val=array('tinyint','smallint','mediumint','int','bigint','decimal','float','double','date','datetime','char','text');
					//strpos($row["Type"],'(');
					$type=substr($row[$currentfieldnum]["Type"],0,strpos($row[$currentfieldnum]["Type"],'('));
					
					foreach($option_val as $key=>$value){
						if ($type==$value){
							$selected='selected';
						}	
				?>		
					<option <?php echo $selected; ?>><?php echo $value; ?></option>	
				<?php $selected= NULL; } ?>
				
				

				</select>
			</td>
			<?php 
			if (strpos($row[$currentfieldnum]["Type"],'(')){
				$len =strlen($row[$currentfieldnum]["Type"]) - (strpos($row[$currentfieldnum]["Type"],'(')+2);
				$length =  substr($row[$currentfieldnum]["Type"],strpos($row[$currentfieldnum]["Type"],'(')+1,$len); 
				
			}else{
				$length=$row[$currentfieldnum]["Type"];
			}
			
			?>
			<td><input name="fieldlength" value="<?php echo $length; ?>"></td>
			<td><input type="checkbox" name="fieldnull" value="1"></td>
			<td>
					<button type="submit" name="deletefield" value="<?php echo $row[$currentfieldnum]["Field"]; ?>">Удалить</button>
					<button type="submit" name="changefield" value="<?php echo $row[$currentfieldnum]["Field"]; ?>">Изменить</button>
				</td>
		</tr>
		<?php }; ?>
		
	</table>
	
	<p>Добавить поле</p>
	<p>
	<table >
		<tr>
			<th>Название поля</th>
			<td>Тип</td>
			<td>Длина</td>
			<td>NULL</td>
			<td>Автоматическое приращение</td>
			
		</tr>		
		<tr>
			<th><input name="fname" value=""></th>
			<td>
				<select name="ftype">
					<option>tinyint</option>
					<option>smallint</option>
					<option>mediumint</option>
					<option selected="">int</option>
					<option>bigint</option>
					<option>decimal</option>
					<option>float</option>
					<option>double</option>
					<option>date</option>
					<option>datetime</option>
					<option>char</option>
					<option>text</option>
				</select>
			</td>
			<td><input name="flength" value=""></td>
			<td><input type="checkbox" name="fnull" value="1"></td>
			
			<td></td>
			
			<td><button type="submit" name="insertfield" value="insertfield">Добавить</button></td>
		</tr>
	</table>
		
	</p>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
</form>
</html>