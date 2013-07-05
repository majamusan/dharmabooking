<?
/* this code is mostly thanks to mataias */
$pluginUrl = PLUGIN_ROOT_URL;
global $wpdb;
$addItemOptions = array('menu Order','item name','minimum','capacity','price','discount price','discription');

if ($_POST['action'] == 'delete') {
	$wpdb->update( $wpdb->prefix.DATABASE_PREFIX.'roomtypes',	array('active' => 0), array('id' => $_POST['deleteItemId'] ));
}
if ($_POST['itemadd'] == 'yes') {
    unset($_POST['itemadd']);
    $sql = 'INSERT INTO '.$wpdb->prefix.DATABASE_PREFIX.'roomtypes 		
				(`menuorder`,`name`,`minimum`,`capacity`,`price`,`discount`,`discription`) 
            VALUES (\''.implode('\',\'',array_values($_POST)).'\')';
    mysql_query($sql);
}
$roomtypes = $wpdb->get_results("SELECT id,menuorder,name,minimum,capacity,price,discount,discription FROM ".$wpdb->prefix.DATABASE_PREFIX."roomtypes WHERE active = 1 ORDER BY menuorder",ARRAY_A );
?>

<link type="text/css" href="<?=$pluginUrl?>admin/styles.css" rel="stylesheet" />
<script type="text/javascript" src="<?=$pluginUrl?>libs/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?=$pluginUrl?>libs/jquery-ui-1.8.5.custom.min.js"></script>
<script type="text/javascript"> var saveRentalAjax = "<?=$pluginUrl?>admin/ajax/saveRental.php"</script>
<script type="text/javascript" src="<?=$pluginUrl?>admin/scripts.js"> </script>
<script type="text/javascript">
<!--
//after rework of functions it can go into main scripts file, if they are nice 

$(function () {
	$(".saveRentalButton").click(function(){	
		SaveRental($(this).closest("tr").find('form').serialize(),$(this).closest("tr"));
	});
	$("input[type=button].rentalcloseButton").click(function () {
	  	$('#info-'+this.id).hide('slow', function() { });
     	$('#blackout').hide('fast', function() { });	
		$('#'+this.id).addClass("edited");
		$('#'+this.id).val("edited");
	});
	$("input[type=button].editButton").click(function () {
	  	$('#info-'+this.id).show('slow', function() { });
     	$('#blackout').show('fast', function() { });	
	});

	$("input[type=button]#addButton").click(function () {
     	$('#addItemDiv').show('slow', function() { });
     	$('#blackout').show('fast', function() { });
	});
	$("input[type=button]#addItemButton").click(function (){
       $("#addItemForm").submit();
	});
	$("input[type=button]#deleteButton").click(function () {
		var nameText = $(this).closest('tr').find('.name').val()
     	if (confirm("Are you sure you want to delete \""+nameText+"\" ?")) {
				$('#deleteItemId').val($(this).closest("tr").find('.rentalId').val());
   	   	$('#deleteItemForm').submit();
      }
		return false;
	});
	//$(this).closest("tr")
	$('#saveEditButton').click(function(){
			$(this).closest("table").find("input[type=text]").clone().appendTo("#theform");
			$(this).closest("table").find("select").clone().appendTo("#theform");
      $('body').css('cursor','wait');
	});
});
-->
</script>
<div id="blackout"></div>
<h3 id="dbox"></h3>
<table  class="rentalRow" cellspacing="0">
	<tr>
		<th>Order</th>
		<th>Name</th>
		<th>Minimum</th>
		<th>Capacity</th>
		<th>Price</th>
		<th>Discount</th>
		<th>Discription</th>
	</tr>
	<? foreach ($roomtypes as $roomtype) { ?>
		<tr>
			<form>
			<? foreach ($roomtype as $fieldName => $value) { ?>
				<? if ($fieldName == 'id') $theId = $value; ?>
				<? if ($fieldName == 'discription') :	?>
					<td> <input type="button" value="edit" class="editButton" id="<?php echo $theId ?>"/>  
						<span class="hidden wizyarea popupup-box" id="info-<?php echo $theId ?>">
						<p>For some reason you must click on the html tab before saving...</p>
						<h4 class="floatright">
							<button class="cancelButton" type="button"><?=__('close',PLUGIN_TRANS_NAMESPACE)?></button>
						</h4>
						<?php wp_editor( stripslashes($value), 'disc'.$theId, array( 'textarea_name' => 'dscription', 'media_buttons' => true, 'teeny' => true));?>
						<div class="clear"></div>	  
						</span>
					</td>
				<?elseif($fieldName == 'id') : ?>
					<input type="hidden" name="id" value="<?=$roomtype['id']?>" id="itemid"/>
				<? else :?>
					<td><input type="text" name="<?=$fieldName?>" value="<?=$value?>" class="<?=$fieldName?>" /> </td>
				<?  endif ?>
			<? } ?>
			<td><button type="button" class="saveRentalButton">Save</button></td>
			<td>
				<input type="hidden" value="<?= $theId?>" class="rentalId" />
				<input type="button" value="Delete" id="deleteButton" />
			</td>
		</tr>
		</form>
	<? } ?>
</table>

<h2><input  type="button" id="addButton" value="<?=__('Add an rental',PLUGIN_TRANS_NAMESPACE)?>" /></h2>

<form id="deleteItemForm" method="POST" action=""> 
	<input type="hidden" name="deleteItemId" id="deleteItemId" />
	<input type="hidden" name="action" value="delete" /> 
</form>

<div id="addItemDiv" class="popupup-box">
	<h2>Add a new rental Item</h2>
	<form id="addItemForm"  method="POST" action="">
    <input type="hidden" name="itemadd" value="yes" />
    <?php foreach($addItemOptions as $anOption ): ?>
		<div class="clear">
        <label for="<?=$anOption?>"><?=ucFirst($anOption)?></label>
		  <input type="text" id="<?=$anOption?>" name="<?=$anOption?>"/>
		</div>  
    <?php endforeach ?>
		<h3>
			<input type="button" value="Cancel" id="cancelButton" /> 
			<input type="button" value="Add" id="addItemButton" />
		</h3>
	</form>
</div>
