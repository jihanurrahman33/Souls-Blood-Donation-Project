<?php
require_once 'models/ForumPost.php';

class ForumController {
    private $forumPostModel;

    public function __construct() {
        $this->forumPostModel = new ForumPost();
    }

    public function index() {
        $posts = $this->forumPostModel->getAll();
        include 'views/forum/index.php';
    }

    public function view($id) {
        $post = $this->forumPostModel->findById($id);
        if (!$post) {
            $_SESSION['errors'][] = 'Post not found.';
            redirect('forum');
        }
        include 'views/forum/view.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'user_id' => $_SESSION['user_id'] ?? null,
                'title' => sanitize($_POST['title'] ?? ''),
                'content' => sanitize($_POST['content'] ?? '')
            ];

            $errors = [];

            if (empty($data['title'])) $errors[] = 'Title is required.';
            if (empty($data['content'])) $errors[] = 'Content is required.';

            if (empty($errors)) {
                try {
                    $this->forumPostModel->create($data);
                    $_SESSION['success'] = 'Post created successfully.';
                    redirect('forum');
                } catch (Exception $e) {
                    $errors[] = 'Failed to create post.';
                }
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['form_data'] = $data;
            }
        }

        include 'views/forum/create.php';
    }

    public function edit($id) {
        $post = $this->forumPostModel->findById($id);
        if (!$post || $post['user_id'] != $_SESSION['user_id']) {
            $_SESSION['errors'][] = 'Unauthorized access.';
            redirect('forum');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => sanitize($_POST['title'] ?? ''),
                'content' => sanitize($_POST['content'] ?? '')
            ];

            $errors = [];

            if (empty($data['title'])) $errors[] = 'Title is required.';
            if (empty($data['content'])) $errors[] = 'Content is required.';

            if (empty($errors)) {
                try {
                    $this->forumPostModel->update($id, $data);
                    $_SESSION['success'] = 'Post updated successfully.';
                    redirect('forum/view/' . $id);
                } catch (Exception $e) {
                    $errors[] = 'Failed to update post.';
                }
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['form_data'] = $data;
            }
        }

        include 'views/forum/edit.php';
    }

    public function delete($id) {
        $post = $this->forumPostModel->findById($id);
        if ($post && $post['user_id'] == $_SESSION['user_id']) {
            $this->forumPostModel->delete($id);
            $_SESSION['success'] = 'Post deleted successfully.';
        } else {
            $_SESSION['errors'][] = 'Unauthorized or post not found.';
        }

        redirect('forum');
    }
}
