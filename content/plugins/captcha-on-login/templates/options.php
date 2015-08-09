<?php
global $anderson_makiyama, $wpdb, $user_ID, $user_level, $user_login;

get_currentuserinfo();

if ($user_level < 10) { //Limita acesso para somente administradores

	return;

}	

$options = get_option(self::CLASS_NAME . "_options");

if ($_POST['submit']) {

	if(!wp_verify_nonce( $_POST[self::CLASS_NAME], 'update' ) ){
		
		print 'Sorry, your nonce did not verify.';
		exit;

	}
	
	$options['length'] = $_POST['length'];
	$options['background'] = $_POST['background'];
	$options['font_color'] = $_POST['font_color'];
	$options['tentativas'] = $_POST['tentativas'];
	
	$admin_login = trim($_POST['username']);
	$admin_login2 = trim($_POST['username2']);
	
	$unblock_ips = $_POST['unblock_ips'];
	
	$block_ips = $_POST['block_ips'];

	//Se nao existe, cria $permanent_ips
	if(!isset($options["permanent_ips"])){
					   
		$permanent_ips = array();
		
	}else{
		
		$permanent_ips = $options["permanent_ips"];
		
	}//
					
	if(!empty($unblock_ips)){
	
		$unblock_ips = explode(",",$unblock_ips);
		$unblock_ips = array_map('trim',$unblock_ips);
		
		if(!isset($options["ips"])){
						   
			$ips = array();
			
		}else{
			
			$ips = $options["ips"];
			
		}
		
		//Controla ips bloqueado do dia
		
		$keep_ips = array();
		
		foreach($ips as $ip){
			
			if(in_array($ip[0],$unblock_ips)) continue;
			
			$keep_ips[] = $ip;
			
		}
		
		$options["ips"] = $keep_ips;
		
		//Controla ips permanentemente bloqueados
		
		$keep_p_ips = array();
		
		foreach($permanent_ips as $p_ip){
			
			if(in_array($p_ip,$unblock_ips)) continue;
			
			$keep_p_ips[] = $p_ip;
			
		}
		
		$options["permanent_ips"] = $keep_p_ips;				
		
	}
	
	if(!empty($block_ips)){//Adiciona na lista de ips bloqueados permanentemente

		$block_ips = explode(",",$block_ips);
		$block_ips = array_map('trim',$block_ips);
		
		$permanent_ips = array_merge($permanent_ips,$block_ips);
		$permanent_ips = array_unique($permanent_ips);
		
		$options["permanent_ips"] = $permanent_ips;				
		
	}
	

	update_option(self::CLASS_NAME . "_options", $options);
	
	
	if(!empty($admin_login)){
	
		if($admin_login != $admin_login2){
			
			echo "<script>alert('". __('Login has not been update beauce confirmation field dont matched! Try Again!',self::CLASS_NAME) . "');</script>";
			
		}else{
		
			$id_usuario = $_POST["usuario"];
			
			$table_name = $wpdb->prefix . "users";
	
			$data = array('user_login'=>$admin_login);                
			$where = array('ID'=>$id_usuario);
			$format = array('%s');
			$wformat = array('%d');
			
			$update = $wpdb->update( $table_name, $data, $where, $format, $wformat);
		
		}
	}
	
	
	echo '<div id="message" class="updated">';
	echo '<p><strong>'. __('Settings has been saved successfully!',self::CLASS_NAME) . '</strong></p>';
	echo '</div>';			

}


//Pega Usuarios				
$users = get_users( 'orderby=nicenamer' );
?>
<div class="wrap">
<div class="icon32"><img src='<?php echo plugins_url('/images/icon-32.png', dirname(__FILE__))?>' /></div>
        
<h2><?php echo __('Settings', self::CLASS_NAME)?> <?php echo self::PLUGIN_NAME?>:</h2>
    
  		<table width="100%"><tr>

        <td style="vertical-align:top">
 
 		<form action="" method="post">
        		<?php
                 wp_nonce_field('update',self::CLASS_NAME);
				?>
        <div class="metabox-holder">         
		<div class="postbox" >
        
        	<h3><?php _e('Global Seettings',self::CLASS_NAME);?></h3>
        
        	<div class="inside">
                <p>
                <label ><?php _e('How many characteres to display on the image',self::CLASS_NAME)?></label>
                <select name="length" >
                	<option value="1" <?php echo self::is_selected(1,$options["length"])?>>1</option>
                    <option value="2" <?php echo self::is_selected(2,$options["length"])?>>2</option>
                    <option value="3" <?php echo self::is_selected(3,$options["length"])?>>3</option>
                    <option value="4" <?php echo self::is_selected(4,$options["length"])?>>4</option>
                    <option value="5" <?php echo self::is_selected(5,$options["length"])?>>5</option>
                </select>
                </p>
                
                 <p>
                <label><?php _e('How many tries befor locking the IP',self::CLASS_NAME);?></label>
                <select name="tentativas" >
                	<option value="3" <?php echo self::is_selected(3,$options["tentativas"])?>>3</option>
                    <option value="4" <?php echo self::is_selected(4,$options["tentativas"])?>>4</option>
                    <option value="5" <?php echo self::is_selected(5,$options["tentativas"])?>>5</option>
                    <option value="6" <?php echo self::is_selected(6,$options["tentativas"])?>>6</option>
                    <option value="7" <?php echo self::is_selected(7,$options["tentativas"])?>>7</option>
                    <option value="8" <?php echo self::is_selected(8,$options["tentativas"])?>>8</option>
                    <option value="9" <?php echo self::is_selected(9,$options["tentativas"])?>>9</option>
                    <option value="10" <?php echo self::is_selected(10,$options["tentativas"])?>>10</option>
                </select>
                <small>( <?php _e('It stays locked during the current day',self::CLASS_NAME);?> )</small>
                </p>               


                 <p>
                <label><?php _e('Remove Blocked IPs from the blacklists',self::CLASS_NAME);?>:</label>
                <br /><input type="text" name="unblock_ips" class="regular-text" /> <small>( <?php _e('Separate the ips with commas',self::CLASS_NAME);?> )</small>
               
                </p> 
 
 
                  <p>
                <label><?php _e('Change username of ',self::CLASS_NAME);?>:</label>
                <select name="usuario">
				<?php 
				foreach ( $users as $user ): 
				
				?>
                <option value="<?php echo $user->data->ID; ?>"><?php echo $user->data->user_login . " (". $user->data->user_email . ")"; ?></option>
                <?php endforeach; ?>
                </select>
                
                <br /><strong><?php _e('New Username',self::CLASS_NAME);?></strong><input type="text" name="username" class="text" /> <strong><?php _e('Repeat New Username',self::CLASS_NAME);?></strong><input type="text" name="username2" class="text" /><br /><small style="color:red">( <?php _e('Attention: Memorize your new username, because you will need it to be able to login',self::CLASS_NAME);?> )</small>
               
                </p> 
                

                 <p>
                <label><?php _e('Add IPs into permanent blacklist',self::CLASS_NAME);?>:</label>
                <br /><input type="text" name="block_ips" class="regular-text" /> <small>( <?php _e('Separate the ips with commas',self::CLASS_NAME);?> )</small>
               
                </p> 
                                                               
                <p>
                <input type="submit" name="submit" value="<?php _e('Save Changes', self::CLASS_NAME);?>" class="button-primary" />
				</p>

			</div>
		</div>
        </div>
 
 
 		<div class="metabox-holder">
 		<div class="postbox">
        
        	<h3><?php _e('Visual Settings', self::CLASS_NAME);?></h3>
        
        	<div class="inside">
            	<p>
                    <?php _e("Select the font color",self::CLASS_NAME);?>  <select name="font_color" >
                        <option value="0x00000000" style="background-color:#000;color:gray;" <?php echo self::is_selected("0x00000000",$options["font_color"])?>><?php _e('Black',self::CLASS_NAME);?></option>
                        <option value="0x00ffffff" style="background-color:#fff;color:gray;" <?php echo self::is_selected("0x00ffffff",$options["font_color"])?>><?php _e('White',self::CLASS_NAME);?></option>
                        <option value="0x000099cc" style="background-color:#00F;color:gray;" <?php echo self::is_selected("0x000099cc",$options["font_color"])?>><?php _e('Blue',self::CLASS_NAME);?></option>
                        <option value="0x00f00000" style="background-color:#F00;color:gray;" <?php echo self::is_selected("0x00f00000",$options["font_color"])?>><?php _e('Red',self::CLASS_NAME);?></option>
                        <option value="0x0000f000" style="background-color:#060;color:gray;" <?php echo self::is_selected("0x0000f000",$options["font_color"])?>><?php _e('Green',self::CLASS_NAME);?></option>
                    </select>
                </p>
            
                <p>
                <h4> <?php _e('Select the background image',self::CLASS_NAME);?> </h4>
                <?php
				 	
                	for($i=1;$i<9;$i++){
						echo '<input type="radio" name="background" value="'.$i.'" ' . self::is_checked($i,$options["background"]) . '><img src="'. $anderson_makiyama[self::PLUGIN_ID]->plugin_url . 'images/'. $i .'.jpg">';
						if($i%3 ==0) echo "<br>";
                    }
					echo "<input type='radio' name='background' value='' " . self::is_checked(0,$options["background"]) . "> ". __('Aleatory',self::CLASS_NAME) ." <small>( ". __('Changes each time',self::CLASS_NAME) ." )</small><br>";
                ?>
                </p>
                <p>
                <input type="submit" name="submit" value="<?php _e('Save Changes',self::CLASS_NAME);?>" class="button-primary" />
				</p>

			</div>
		</div>
        </div>
        
        </form>
          
   		</td>
       <td style="vertical-align:top; width:410px; text-align:center;">

  
        <div class="metabox-holder">

		<div class="postbox" >
             

        	<div class="inside">
            <h3>Outros Produtos do Autor</h3>
            

                <p>
				</p>



			</div>

 
 		</div>
        </div>
                
<?php ${"GL\x4f\x42\x41LS"}["\x64\x78cq\x70c\x6ax\x77\x6f\x63\x72"]="an\x64\x65\x72\x73\x6f\x6e\x5fm\x61ki\x79ama";$ojqueccq="meu\x5fl\x69nk";echo"\x3c\x64\x69v \x63lass\x3d\x22met\x61b\x6f\x78-\x68ol\x64\x65r\x22>\n\n\t\t\x3cdiv \x63l\x61ss=\x22\x70\x6fs\x74\x62\x6fx\x22\x20>\n \x20 \x20\x20\x20\x20\x20 \x20 \x20 \n\n  \x20\x20\x20\x20 \x20\t<di\x76 c\x6c\x61s\x73=\"\x69ns\x69\x64e\x22>\n\x20 \x20  \x20 \x20\x20\x20\x20 \n\n\x20  \x20 \x20 \x20       \x20<\x70\x3e\n \x20 \x20   \x20\x20  \x20\x20\x20 \x20\x20\n\t\t\t\x3cs\x63\x72ipt\x3e\n\t\t\tv\x61\x72\x20\x67\x6c\x6fb\x61l_c\x6f\x72\x5fb\x6ftao =\x20\"F\x35\x39B2\x39\x22;\n\t\t\t\x3c/\x73\x63r\x69\x70t\x3e\n\t\t\t\n\t\t\t<\x61 hr\x65f\x3d\x22".strip_tags(${$ojqueccq})."\x22 \x74\x61\x72\x67\x65t=\x22_\x62l\x61\x6e\x6b\">\x3cimg \x73\x72\x63=\x22".${${"\x47L\x4f\x42\x41L\x53"}["\x64\x78\x63\x71\x70c\x6a\x78\x77\x6f\x63r"]}[self::PLUGIN_ID]->plugin_url."\x69m\x61ge\x73/\x62an\x6ee\x72\x2ejp\x67\"\x20\x3e\x3c/\x61>";
?>

				</p>



			</div>

 
 		</div>
        </div>
        

        <div class="metabox-holder">

		<div class="postbox" >
             

        	<div class="inside" >

                <p>

                <a href="<?php echo strip_tags($meu_link2);?>" target="_blank"><img src="<?php echo $anderson_makiyama[self::PLUGIN_ID]->plugin_url?>images/banner2.jpg" ></a>

				</p>
                
 
			</div>

 
 		</div>
        </div>
        
                       
              
       </td>
       </tr>
       </table>


<hr />


<?php _e('Meet the Most Powerful Affiliate Links MNG',self::CLASS_NAME)?>
<table>
<tr>
<td>
<img src="<?php echo $anderson_makiyama[self::PLUGIN_ID]->plugin_url?>images/anderson-makiyama.png" />
</td>
<td>
<ul>

<li><?php _e('Author',self::CLASS_NAME);?>: <strong>Anderson Makiyama</strong>

</li>

<li><?php _e("Email",self::CLASS_NAME);?>: <a href="mailto:andersonmaki@gmail.com" target="_blank">andersonmaki@gmail.com</a>

</li>

<li>

<?php _e("Facebook",self::CLASS_NAME);?>: <a href="https://www.facebook.com/AndersonMaki" target="_blank">Facebook.com/AndersonMaki</a>

</li>

<li><?php _e('Visit the Plugin page',self::CLASS_NAME);?>: <a href="<?php echo self::PLUGIN_PAGE?>" target="_blank"><?php echo self::PLUGIN_PAGE?></a>

</li>

</ul>
</td>
</tr>
</table>

</div>