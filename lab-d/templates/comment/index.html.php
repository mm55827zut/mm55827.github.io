<?php
$title = 'Comments';
$bodyClass = 'index';
$escape = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
ob_start(); ?>
    <h1>Comments</h1>
    <a href="<?= $router->generatePath('comment-create') ?>">Create new comment</a>

    <ul class="index-list">
        <?php foreach ($comments as $comment): ?>
            <li>
                <p><?= nl2br($escape($comment->getContent())) ?></p>
                <p>Post #<?= $comment->getPostId() ?></p>
                <?php if ($comment->getGifPath()): ?>
                    <img src="<?= $escape($comment->getGifPath()) ?>" alt="Comment GIF" style="max-width: 300px">
                <?php endif; ?>
                <ul class="action-list">
                    <li><a href="<?= $router->generatePath('comment-show', ['id' => $comment->getId()]) ?>">Details</a></li>
                    <li><a href="<?= $router->generatePath('comment-edit', ['id' => $comment->getId()]) ?>">Edit</a></li>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
<?php $main = ob_get_clean();
include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';