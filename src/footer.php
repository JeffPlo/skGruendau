<?php global $bambee, $bambeeWebsite; ?>
</div>
</main>

<footer>
    <?php
    wp_nav_menu(
        array(
            'echo' => true,
            'container' => 'div',                   // remove nav container
            'container_class' => 'row text-center', // class of container
            'menu' => '',                      	    // menu name
            'menu_class' => 'menu-footer',         // adding custom nav class
            'before' => '',                         // before each link <a>
            'after' => '',                          // after each link </a>
            'link_before' => '',                    // before each link text
            'link_after' => '',
            'depth' => 1
        )
    );
    ?>
</footer>
</div>

<?php wp_footer(); ?>
</body>
</html>
