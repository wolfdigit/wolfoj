<div style="top:0; left:0; right:0; width:auto; padding:5px; background-color:#900; color:white">
<span style="float:left">老狼的資科概 - 召喚獸！</span>
<span style="float:right"><?php
$user = get_instance()->auth->user();
if ($user) {
	echo $user;
	echo ' | <a href="'.site_url('User/logout').'">logout</a>';
}
else {
	echo '<a href="'.site_url('User/login').'">login</a>';
}
?></span>
<center><del>咬我啊笨蛋！</del></center>
</div>
<?php

