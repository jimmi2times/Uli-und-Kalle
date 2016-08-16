 <?php
 /*
  * Footer Datei
  * Hier passiert nicht viel
  * Wenn User = Admin Querys ausgeben
  */ 

 ?>

	<!-- Ende Content -->
	</div> 
<? print_footer_menu(); ?>
<!-- Ende Page -->
</div> 
 <?php 
 global $wpdb, $user_ID;
 if ($user_ID == 1){
 printf(__('%d queries. %.4f seconds.','k2_domain'), $wpdb->num_queries , timer_stop()); 
 }
 ?>
</body>
</html>

