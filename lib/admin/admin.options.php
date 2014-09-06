<?php

// Restrict to Editors
if ( current_user_can('edit_others_posts') )
{
	
?>

		<div id="zp-Zotpress" class="wrap">
            
            <?php include('admin.display.tabs.php'); ?>
			
			<div id="zp-Options-Wrapper">
				
				<h3>Defaults</h3>
				
				<?php include('admin.options.form.php'); ?>
				
				
				<hr />
				
				
				<?php if ( ZOTPRESS_EXPERIMENTAL_EDITOR ): ?>
				<!-- START OF EDITOR -->
				<div class="zp-Column-1">
					<div class="zp-Column-Inner">
						
						<h4>Editor Features</h4>
						
						<p class="note">Enable or disable the word processor-like features in the rich text editor.</p>
						
						<div id="zp-Zotpress-Options-Editor" class="zp-Zotpress-Options">
							
							<label for="zp-Zotpress-Options-Editor">Enable editor features?</label>
							<select id="zp-Zotpress-Options-Editor">
								<?php
								
								// Determine default editor features status
								$zp_default_editor = "editor_enable";
								if (get_option("Zotpress_DefaultEditor")) $zp_default_editor = get_option("Zotpress_DefaultEditor");
								
								?>
								<option id="editor_enable" value="editor_enable" <?php if ( $zp_default_editor == "editor_enable" ) { ?>selected='selected'<?php } ?>>Enable</option>
								<option id="editor_disable" value="editor_disable" <?php if ( $zp_default_editor == "editor_disable" ) { ?>selected='selected'<?php } ?>>Disable</option>
							</select>
							
							<script type="text/javascript" >
							jQuery(document).ready(function() {
							
								jQuery("#zp-Zotpress-Options-Editor-Button").click(function()
								{
									// Plunk it together
									var data = 'submit=true&editor=' + jQuery('select#zp-Zotpress-Options-Editor').val();
									
									// Prep for data validation
									jQuery(this).attr('disabled','true');
									jQuery('#zp-Zotpress-Options-Editor .zp-Loading').show();
									
									// Set up uri
									var xmlUri = '<?php echo ZOTPRESS_PLUGIN_URL; ?>lib/widget/widget.metabox.actions.php?'+data;
									
									// AJAX
									jQuery.get(xmlUri, {}, function(xml)
									{
										var $result = jQuery('result', xml).attr('success');
										
										jQuery('#zp-Zotpress-Options-Editor .zp-Loading').hide();
										jQuery('input#zp-Zotpress-Options-Editor-Button').removeAttr('disabled');
										
										if ($result == "true")
										{
											jQuery('#zp-Zotpress-Options-Editor div.zp-Errors').hide();
											jQuery('#zp-Zotpress-Options-Editor div.zp-Success').show();
											
											jQuery.doTimeout(1000,function() {
												jQuery('#zp-Zotpress-Options-Editor div.zp-Success').hide();
											});
										}
										else // Show errors
										{
											jQuery('#zp-Zotpress-Options-Editor div.zp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
											jQuery('#zp-Zotpress-Options-Editor div.zp-Errors').show();
										}
									});
									
									// Cancel default behaviours
									return false;
									
								});
								
							});
							</script>
							
							<input type="button" id="zp-Zotpress-Options-Editor-Button" class="button-secondary" value="Set Editor Features" />
							<div class="zp-Loading">loading</div>
							<div class="zp-Success">Success!</div>
							<div class="zp-Errors">Errors!</div>
							
						</div>
					</div>
				</div><!-- END OF EDITOR -->
				<?php endif; ?>
				
				
				
				<!-- START OF CPT -->
				<div class="zp-Column-1">
					<div class="zp-Column-Inner">
						
						<h4>Set Reference Widget</h4>
						
						<p class="note">Enable or disable the Zotpress Reference widget for specific post types.</p>
						
						<div id="zp-Zotpress-Options-CPT" class="zp-Zotpress-Options">
							
							<?php
							
							// See if default exists
                            $zp_default_cpt = "post,page";
                            if (get_option("Zotpress_DefaultCPT"))
                                $zp_default_cpt = get_option("Zotpress_DefaultCPT");
							$zp_default_cpt = explode(",",$zp_default_cpt);
							
							$post_types = get_post_types( '', 'names' ); 
							
							foreach ( $post_types as $post_type )
							{
								echo "<div class='zp-CPT-Checkbox'>";
								echo "<input type=\"checkbox\" name=\"zp-CTP\" id=\"".$post_type."\" value=\"".$post_type."\" ";
								//if ( in_array( $post_type, $zp_default_cpt ) ) echo "disabled=\"disabled\" checked ";
								if ( in_array( $post_type, $zp_default_cpt ) ) echo "checked ";
								echo "/>";
								echo "<label ";
								//if ( in_array( $post_type, $zp_default_cpt ) )  echo "class=\"dis\" ";
								echo "for=\"".$post_type."\">".$post_type."</label>";
								echo "</div>\n";
							}
							
							?>
							
							<script type="text/javascript" >
							jQuery(document).ready(function() {
							
								jQuery("#zp-Zotpress-Options-CPT-Button").click(function()
								{
									// Get all post types
									var zpTempCPT = "";
									jQuery("input[name='zp-CTP']:checked").each(function()
									{
										zpTempCPT = zpTempCPT + "," + jQuery(this).val();
									});
									
									// Plunk it together
									var data = 'submit=true&cpt=' + zpTempCPT.substring(1);
									
									// Prep for data validation
									jQuery(this).attr('disabled','true');
									jQuery('#zp-Zotpress-Options-CPT .zp-Loading').show();
									
									// Set up uri
									var xmlUri = '<?php echo ZOTPRESS_PLUGIN_URL; ?>lib/widget/widget.metabox.actions.php?'+data;
									
									// AJAX
									jQuery.get(xmlUri, {}, function(xml)
									{
										var $result = jQuery('result', xml).attr('success');
										
										jQuery('#zp-Zotpress-Options-CPT .zp-Loading').hide();
										jQuery('input#zp-Zotpress-Options-CPT-Button').removeAttr('disabled');
										
										if ($result == "true")
										{
											jQuery('#zp-Zotpress-Options-CPT div.zp-Errors').hide();
											jQuery('#zp-Zotpress-Options-CPT div.zp-Success').show();
											
											jQuery.doTimeout(1000,function() {
												jQuery('#zp-Zotpress-Options-CPT div.zp-Success').hide();
											});
										}
										else // Show errors
										{
											jQuery('#zp-Zotpress-Options-CPT div.zp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
											jQuery('#zp-Zotpress-Options-CPT div.zp-Errors').show();
										}
									});
									
									// Cancel default behaviours
									return false;
									
								});
								
							});
							</script>
							
							<input type="button" id="zp-Zotpress-Options-CPT-Button" class="button-secondary" value="Set Reference Widget" />
							<div class="zp-Loading">loading</div>
							<div class="zp-Success">Success!</div>
							<div class="zp-Errors">Errors!</div>
							
						</div>
					</div>
				</div><!-- END OF EDITOR -->
				
				
				
				<!-- START OF RESET -->
				<div class="zp-Column-1">
					<div class="zp-Column-Inner">
						
						<h4>Reset Zotpress</h4>
						
						<p class="note">Note: This action will clear all database entries associated with Zotpress, including account information and citations&#8212;it <strong>cannot be undone</strong>. Proceed with caution.</p>
						
						<div id="zp-Zotpress-Options-Reset" class="zp-Zotpress-Options">
							
							<script type="text/javascript" >
							jQuery(document).ready(function() {
							
								jQuery("#zp-Zotpress-Options-Reset-Button").click(function()
								{
									var confirmDelete = confirm("Are you sure you want to reset Zotpress? This cannot be undone.");
									
									if ( confirmDelete == true )
									{
										// Prep for data validation
										jQuery(this).attr( 'disabled', 'true' );
										jQuery('#zp-Zotpress-Options-Reset .zp-Loading').show();
										
										jQuery.get( '<?php echo ZOTPRESS_PLUGIN_URL; ?>lib/widget/widget.metabox.actions.php?submit=true&reset=true', {}, function(xml)
										{
											var $result = jQuery('result', xml).attr('success');
											
											jQuery('#zp-Zotpress-Options-Reset .zp-Loading').hide();
											jQuery('input#zp-Zotpress-Options-Reset-Button').removeAttr('disabled');
											
											if ($result == "true")
											{
												jQuery('#zp-Zotpress-Options-Reset div.zp-Errors').hide();
												jQuery('#zp-Zotpress-Options-Reset div.zp-Success').show();
												
												jQuery.doTimeout(1000,function() {
													jQuery('#zp-Zotpress-Options-Reset div.zp-Success').hide();
													window.parent.location = "<?php echo ZOTPRESS_PLUGIN_URL; ?>../../../wp-admin/admin.php?page=Zotpress";
												});
											}
											else // Show errors
											{
												jQuery('#zp-Zotpress-Options-Reset div.zp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
												jQuery('#zp-Zotpress-Options-Reset div.zp-Errors').show();
											}
										});
									} // confirmDelete
									
									// Cancel default behaviours
									return false;
									
								});
								
							});
							</script>
							
							<input type="button" id="zp-Zotpress-Options-Reset-Button" class="button-secondary" value="Reset Zotpress" />
							<div class="zp-Loading">loading</div>
							<div class="zp-Success">Success!</div>
							<div class="zp-Errors">Errors!</div>
							
						</div>
					</div>
				</div><!-- END OF RESET -->
				
			</div><!-- zp-Browse-Wrapper -->
		
		</div>
	
<?php

} // !current_user_can('edit_others_posts')

else
{
	echo "<p>Sorry, you don't have permission to access this page.</p>";
}

?>