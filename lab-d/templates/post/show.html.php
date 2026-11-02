<?php

/** @var \App\Model\Post $post */
/** @var \App\Service\Router $router */

$title = "{$post->getSubject()} ({$post->getId()})";
$bodyClass = 'show';

ob_start(); ?>
    <h1><?= $post->getSubject() ?></h1>
    <article>
        <?= $post->getContent();?>
    </article>
    <h2>Comments</h2>
    <p>
        <a href="<?= $router->generatePath('comment-create', ['post_id' => $post->getId()]) ?>">
            Add comment
        </a>
    </p>

    <ul>
        <?php foreach ($comments as $comment): ?>
            <li>
                <p><?= nl2br(htmlspecialchars($comment->getContent(), ENT_QUOTES, 'UTF-8')) ?></p>
                <?php if ($comment->getGifPath()): ?>
                    <img src="<?= htmlspecialchars($comment->getGifPath(), ENT_QUOTES, 'UTF-8') ?>"
                         alt="Comment GIF" style="max-width: 300px">
                <?php endif; ?>
                <a href="<?= $router->generatePath('comment-show', ['id' => $comment->getId()]) ?>">
                    Comment details
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <ul class="action-list">
        <li> <a href="<?= $router->generatePath('post-index') ?>">Back to list</a></li>
        <li><a href="<?= $router->generatePath('post-edit', ['id'=> $post->getId()]) ?>">Edit</a></li>
    </ul>
<?php $main = ob_get_clean();

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
