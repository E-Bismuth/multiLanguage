<?php

require_once("Language.class.php");

$Lang = new Language();
?>

Actual Language : <?= $Lang->lang;?>
<br>
Other Language : <select onChange="window.location.assign(this.value)">
				<?php foreach($Lang->Urls AS $CodeLang=>$Url){?>
					<option value="<?= $Url;?>"><?= $CodeLang;?></option>
				<?php }?>