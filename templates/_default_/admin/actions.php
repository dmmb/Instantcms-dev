<?php if ($actions) { ?>
    <div class="actions_list">
        <?php foreach($actions as $action) { ?>
            <div class="action_entry act_<?php echo $action['name']; ?>">
                <div class="action_date
                    <?php if ($action['is_new']){ ?> is_new<?php } ?>"><?php echo $action['pubdate']; ?> �����
                    <a href="/actions/delete/<?php echo $action['id']; ?>" class="action_delete" title="�������" onclick="if(!confirm('������� ������ �� �����?')){ return false; }"></a>
                </div>
                <div class="action_title">
                    <a href="<?php echo $action['user_url']; ?>" class="action_user"><?php echo $action['user_nickname']; ?></a>
                    <?php if ($action['message']) { ?>
                        <?php echo $action['message']; ?><?php if ($action['description']) { ?>:<?php } ?>
                    <?php } else { ?>
                        <?php if ($action['description']){ ?>
                            &rarr; <?php echo $action['description']; ?>
                        <?php } ?>
                    <?php } ?>
                </div>
                <?php if ($action['message']) { ?>
                    <?php if ($action['description']) { ?>
                        <div class="action_details"><?php echo $action['description']; ?></div>
                    <?php } ?>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>
