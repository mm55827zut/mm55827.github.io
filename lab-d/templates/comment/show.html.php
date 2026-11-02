<?php
$title = 'Comment #' . $comment->getId();
$bodyClass = 'show';
$escape = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
ob_start(); ?>
    <h1>Comment #<?= $comment->getId() ?></h1>
    <p>Post #<?= $comment->getPostId() ?></p>
    <p><?= nl2br($escape($comment->getContent())) ?></p>

<?php if ($comment->getGifPath()): ?>
    <img src="<?= $escape($comment->getGifPath()) ?>" alt="Comment GIF" style="max-width: 400px">
<?php endif; ?>

    <ul class="action-list">
        <li><a href="<?= $router->generatePath('comment-index') ?>">Comments</a></li>
        <li><a href="<?= $router->generatePath('comment-edit', ['id' => $comment->getId()]) ?>">Edit</a></li>
    </ul>
<?php $main = ob_get_clean();
include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';