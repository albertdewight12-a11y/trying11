document.addEventListener('DOMContentLoaded', () => {
    // Like functionality
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const formData = new FormData();
            formData.append('post_id', postId);

            fetch('like.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    this.innerHTML = (data.status === 'liked' ? '❤️' : '🤍') + ' ' + data.count;
                }
            });
        });
    });

    // Follow functionality
    const followBtn = document.querySelector('.follow-btn');
    if (followBtn) {
        followBtn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const formData = new FormData();
            formData.append('following_id', userId);

            fetch('follow.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    this.textContent = data.status === 'followed' ? 'Unfollow' : 'Follow';
                    this.classList.toggle('btn-secondary');
                }
            });
        });
    }

    // Comment toggle
    document.querySelectorAll('.comment-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const commentSection = document.getElementById(`comments-${postId}`);
            commentSection.style.display = commentSection.style.display === 'none' ? 'block' : 'none';
        });
    });

    // AJAX Comment Submission
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const postId = formData.get('post_id');
            const commentList = document.getElementById(`comment-list-${postId}`);

            fetch('comment.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const newComment = document.createElement('div');
                    newComment.style.fontSize = '0.9rem';
                    newComment.style.marginBottom = '5px';
                    newComment.innerHTML = `<strong>${data.username}</strong> ${data.comment_text}`;
                    commentList.appendChild(newComment);
                    this.reset();
                } else {
                    alert(data.error || 'Failed to post comment');
                }
            });
        });
    });
});
