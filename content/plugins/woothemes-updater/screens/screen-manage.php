<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="col-container" class="about-wrap">
	<?php
	echo '<div class="updated">' . wpautop( sprintf( __( 'See below for a list of the WooThemes products in use on %s. You can %s, as well as our %s on how this works. %s', 'woothemes-updater' ), get_bloginfo( 'name' ), '<a href="https://www.woothemes.com/my-account/my-licenses">view your licenses here</a>', '<a href="http://docs.woothemes.com/document/woothemes-helper/?utm_source=helper">documentation</a>', '&nbsp;&nbsp;<a href="' . esc_url( add_query_arg( array( 'force-check' => '1' ), admin_url( 'update-core.php' ) ) ) . '" class="button">' . __( 'Check for Updates', 'woothemes-updater' ) . '</a>' ) ) . '</div>' . "\n";
	?>
		<div class="col-wrap">
			<form id="activate-products" method="post" action="" class="validate">
				<input type="hidden" name="action" value="activate-products" />
				<input type="hidden" name="page" value="<?php echo esc_attr( $this->page_slug ); ?>" />
				<?php
				require_once( $this->classes_path . 'class-woothemes-updater-licenses-table.php' );
				$this->list_table = new WooThemes_Updater_Licenses_Table();
				$this->list_table->data = $this->get_detected_products();
				$this->list_table->prepare_items();
				$this->list_table->display();
				submit_button( __( 'Activate Products', 'woothemes-updater' ), 'button-primary' );
				?>
				<?php wp_nonce_field( 'wt-helper-activate-license', 'wt-helper-nonce' ); ?>
			</form>
		</div><!--/.col-wrap-->
</div><!--/#col-container-->