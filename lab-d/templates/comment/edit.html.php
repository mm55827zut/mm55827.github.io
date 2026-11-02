<?php
$title = 'Edit Comment';
$bodyClass = 'edit';
ob_start(); ?>
    <h1>Edit Comment #<?= $comment->getId() ?></h1>

<?php if ($comment->getGifPath()): ?>
    <p>Current GIF:</p>
    <img src="<?= htmlspecialchars($comment->getGifPath(), ENT_QUOTES, 'UTF-8') ?>"
         alt="Comment GIF" style="max-width: 400px">
<?php endif; ?>

    <form action="<?= $router->generatePath('comment-edit') ?>"
          method="post" enctype="multipart/form-data" class="edit-form">
        <?php require __DIR__ . DIRECTORY_SEPARATOR . 'form.html.php'; ?>
        <input type="hidden" name="action" value="comment-edit">
        <input type="hidden" name="id" value="<?= $comment->getId() ?>">
    </form>

    <form action="<?= $router->generatePath('comment-delete') ?>" method="post">
        <input type="hidden" name="action" value="comment-delete">
        <input type="hidden" name="id" value="<?= $comment->getId() ?>">
        <input type="submit" value="Delete" onclick="return confirm('Are you sure?')">
    </form>
    <a href="<?= $router->generatePath('comment-index') ?>">Back to list</a>
<?php $main = ob_get_clean();
include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';