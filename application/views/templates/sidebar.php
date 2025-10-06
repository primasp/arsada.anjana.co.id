<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">Main</li>


                <?php
                $menu = get_menu();
                // return var_dump($menu);
                // die;
                $current_url = current_url();

                foreach ($menu as $item) {


                    $is_active = $current_url == $item['url'];
                    $has_active_submenu = false;

                    if (!empty($item['submenu'])) {
                        foreach ($item['submenu'] as $submenu) {
                            if ($current_url == $submenu['url']) {
                                $has_active_submenu = true;
                                break;
                            }
                        }
                    }





                    if (empty($item['submenu'])) { ?>
                        <li class="<?= $is_active ? 'active' : '' ?>">
                            <a href="<?= $item['url'] ?>">
                                <span class="menu-side">
                                    <img src="<?= base_url($item['icon']) ?>" alt="">
                                </span>
                                <span><?= $item['label'] ?></span>
                            </a>
                        </li>
                    <?php } else { ?>
                        <!-- <li class="submenu <?= $has_active_submenu ? 'active' : '' ?>">
                            <a href="#" class="<?= $has_active_submenu ? 'subdrop' : '' ?>">
                                <span class="menu-side">
                                    <img src="<?= base_url($item['icon']) ?>" alt="">
                                </span>
                                <span><?= $item['label'] ?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul style="<?= $has_active_submenu ? 'display: block;' : 'display: none;' ?>">
                                <?php foreach ($item['submenu'] as $submenu) {
                                    $is_submenu_active = $current_url == $submenu['url'];
                                ?>
                                    <li class="<?= $is_submenu_active ? 'active' : '' ?>">
                                        <a href="<?= $submenu['url'] ?>">
                                            <?= $submenu['label'] ?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li> -->


                        <li class="submenu">
                            <a href="#"><span class="menu-side"><img src="<?= base_url($item['icon']) ?>" alt=""></span>
                                <span><?= $item['label'] ?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul style="display: none;">
                                <?php foreach ($item['submenu'] as $submenu) {
                                    $is_submenu_active = $current_url == $submenu['url'];
                                ?>
                                    <li>
                                        <a class="<?= $is_submenu_active ? 'active' : '' ?>" href="<?= $submenu['url'] ?>">
                                            <?= $submenu['label'] ?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>









                <?php }
                } ?>

                <li>
                    <a href="<?php echo site_url('Logout'); ?>"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
                </li>


            </ul>
        </div>
    </div>
</div>



<div class="page-wrapper">