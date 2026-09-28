<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const commentsList = document.getElementById('comments-list');
        const commentsCount = document.getElementById('comments-count');
        const publicationLikesCount = document.getElementById('publication-likes-count');

        function showMessage(message, type = 'success') {
            const wrapper = document.querySelector('.publication-engagement');
            if (!wrapper) return;

            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show shadow-sm ajax-alert`;
            alert.setAttribute('role', 'alert');
            alert.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'} me-1"></i>
            <span>${escapeHtml(message)}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
            wrapper.prepend(alert);
            setTimeout(() => alert.remove(), 4000);
        }

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value ?? '';
            return div.innerHTML;
        }

        function setLoading(form, isLoading) {
            const button = form.querySelector('button[type="submit"]');
            if (!button) return;

            if (isLoading) {
                button.dataset.originalHtml = button.innerHTML;
                button.disabled = true;
                button.innerHTML = `
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Please wait...
            `;
            } else {
                button.disabled = false;
                if (button.dataset.originalHtml) {
                    button.innerHTML = button.dataset.originalHtml;
                    delete button.dataset.originalHtml;
                }
            }
        }

        function updatePublicationLike(data, form) {
            if (publicationLikesCount) {
                publicationLikesCount.textContent = data.likes_count;
            }

            const button = form.querySelector('button[type="submit"]');
            const icon = button?.querySelector('i');
            const label = button?.querySelector('.like-label');
            if (!button || !icon || !label) return;

            if (data.liked) {
                button.classList.remove('btn-outline-danger');
                button.classList.add('btn-danger');
                icon.classList.remove('far');
                icon.classList.add('fas');
                label.textContent = 'Unlike';
            } else {
                button.classList.remove('btn-danger');
                button.classList.add('btn-outline-danger');
                icon.classList.remove('fas');
                icon.classList.add('far');
                label.textContent = 'Like';
            }
        }

        function updateCommentLike(data, form) {
            const button = form.querySelector('button[type="submit"]');
            const icon = button?.querySelector('i');
            const count = button?.querySelector('.like-count');
            const label = button?.querySelector('.like-label');
            if (!button || !icon || !count || !label) return;

            count.textContent = data.likes_count;

            if (data.liked) {
                button.classList.remove('text-muted');
                button.classList.add('text-danger');
                icon.classList.remove('far');
                icon.classList.add('fas');
                label.textContent = 'Unlike';
            } else {
                button.classList.remove('text-danger');
                button.classList.add('text-muted');
                icon.classList.remove('fas');
                icon.classList.add('far');
                label.textContent = 'Like';
            }
        }

        // ===== CREATE MAIN COMMENT HTML =====
        function createMainCommentHtml(comment) {
            const currentUsername = @json(auth()->check() ? auth()->user()->username : null);
            const isOwner = currentUsername && comment.username === currentUsername;

            const likeUrl = window.publicationConfig.commentLikeUrl.replace('__UUID__', encodeURIComponent(
                comment.uuid));
            const updateUrl = window.publicationConfig.commentUpdateUrl.replace('__UUID__', encodeURIComponent(
                comment.uuid));
            const deleteUrl = window.publicationConfig.commentDeleteUrl.replace('__UUID__', encodeURIComponent(
                comment.uuid));
            const replyUrl = window.publicationConfig.replyStoreUrl.replace('__UUID__', encodeURIComponent(
                comment.uuid));

            return `
            <div class="comment-item border-bottom py-4" id="comment-${escapeHtml(comment.uuid)}">
                <div class="d-flex gap-3">
                    <div class="comment-avatar">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                             style="width: 42px; height: 42px; font-weight: 700;">
                            ${escapeHtml(comment.initial)}
                        </div>
                    </div>

                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <strong class="text-dark">${escapeHtml(comment.name)}</strong>
                            <small class="text-muted">${escapeHtml(comment.created_at)}</small>
                        </div>

                        <p class="comment-content mt-2 mb-2" id="comment-text-${escapeHtml(comment.uuid)}">
                            ${escapeHtml(comment.comment)}
                        </p>

                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <form method="POST" action="${likeUrl}" class="d-inline js-ajax-form" data-action-type="comment-like">
                                <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                                <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none text-muted">
                                    <i class="far fa-heart"></i>
                                    <span class="ms-1 like-count">0</span>
                                    <span class="ms-1 like-label">Like</span>
                                </button>
                            </form>

                            <button type="button" class="btn btn-sm btn-link p-0 text-muted reply-toggle-btn"
                                    data-target="reply-form-${escapeHtml(comment.uuid)}">
                                <i class="fas fa-reply me-1"></i> Reply
                            </button>

                            ${isOwner ? `
                                <button type="button" class="btn btn-sm btn-link p-0 text-primary edit-toggle-btn"
                                        data-target="edit-form-${escapeHtml(comment.uuid)}">
                                    <i class="fas fa-pen me-1"></i> Edit
                                </button>

                                <form method="POST" action="${deleteUrl}"
                                      class="d-inline js-ajax-form"
                                      data-action-type="delete-comment"
                                      data-comment-uuid="${escapeHtml(comment.uuid)}">
                                    <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-link p-0 text-danger">
                                        <i class="fas fa-trash-can me-1"></i> Delete
                                    </button>
                                </form>
                            ` : ''}
                        </div>

                        ${isOwner ? `
                            <form method="POST" action="${updateUrl}"
                                  id="edit-form-${escapeHtml(comment.uuid)}"
                                  class="edit-comment-form mt-3 d-none js-ajax-form"
                                  data-action-type="update-comment"
                                  data-comment-uuid="${escapeHtml(comment.uuid)}">
                                <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                                <input type="hidden" name="_method" value="PUT">
                                <textarea name="comment" rows="3" maxlength="3000" class="form-control" required>${escapeHtml(comment.comment)}</textarea>
                                <div class="d-flex gap-2 mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm rounded-2">
                                        <i class="fas fa-check me-1"></i> Update Comment
                                    </button>
                                    <button type="button" class="btn btn-light border btn-sm rounded-2 cancel-toggle-btn"
                                            data-target="edit-form-${escapeHtml(comment.uuid)}">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        ` : ''}

                        <form method="POST" action="${replyUrl}"
                              id="reply-form-${escapeHtml(comment.uuid)}"
                              class="reply-form mt-3 d-none js-ajax-form"
                              data-action-type="create-reply"
                              data-parent-uuid="${escapeHtml(comment.uuid)}">
                            <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                            <textarea name="comment" rows="2" maxlength="3000" class="form-control"
                                      placeholder="Write a reply..." required></textarea>
                            <div class="d-flex gap-2 mt-2">
                                <button type="submit" class="btn btn-primary btn-sm rounded-2">
                                    <i class="fas fa-paper-plane me-1"></i> Reply
                                </button>
                                <button type="button" class="btn btn-light border btn-sm rounded-2 cancel-toggle-btn"
                                        data-target="reply-form-${escapeHtml(comment.uuid)}">
                                    Cancel
                                </button>
                            </div>
                        </form>

                        <div class="comment-replies mt-3"></div>
                    </div>
                </div>
            </div>
        `;
        }

        // ===== CREATE REPLY HTML =====
        function createReplyHtml(reply) {
            const currentUsername = @json(auth()->check() ? auth()->user()->username : null);
            const isOwner = currentUsername && reply.username === currentUsername;

            const likeUrl = window.publicationConfig.commentLikeUrl.replace('__UUID__', encodeURIComponent(reply
                .uuid));
            const updateUrl = window.publicationConfig.commentUpdateUrl.replace('__UUID__', encodeURIComponent(
                reply.uuid));
            const deleteUrl = window.publicationConfig.commentDeleteUrl.replace('__UUID__', encodeURIComponent(
                reply.uuid));

            return `
            <div class="comment-reply ms-md-4 mt-3 ps-3 border-start" id="comment-${escapeHtml(reply.uuid)}">
                <div class="d-flex gap-2">
                    <div class="rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 34px; height: 34px; font-size: 13px; font-weight: 700;">
                        ${escapeHtml(reply.initial)}
                    </div>

                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <strong class="small text-dark">${escapeHtml(reply.name)}</strong>
                            <small class="text-muted">${escapeHtml(reply.created_at)}</small>
                        </div>

                        <p class="comment-content mb-2 mt-1" id="comment-text-${escapeHtml(reply.uuid)}">
                            ${escapeHtml(reply.comment)}
                        </p>

                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <form method="POST" action="${likeUrl}" class="d-inline js-ajax-form" data-action-type="comment-like">
                                <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                                <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none text-muted">
                                    <i class="far fa-heart"></i>
                                    <span class="ms-1 like-count">${Number(reply.likes_count ?? 0)}</span>
                                    <span class="ms-1 like-label">Like</span>
                                </button>
                            </form>

                            ${isOwner ? `
                                <button type="button" class="btn btn-sm btn-link p-0 text-primary edit-toggle-btn"
                                        data-target="edit-form-${escapeHtml(reply.uuid)}">
                                    <i class="fas fa-pen me-1"></i> Edit
                                </button>

                                <form method="POST" action="${deleteUrl}"
                                      class="d-inline js-ajax-form"
                                      data-action-type="delete-comment"
                                      data-comment-uuid="${escapeHtml(reply.uuid)}">
                                    <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-link p-0 text-danger">
                                        <i class="fas fa-trash-can me-1"></i> Delete
                                    </button>
                                </form>
                            ` : ''}
                        </div>

                        ${isOwner ? `
                            <form method="POST" action="${updateUrl}"
                                  id="edit-form-${escapeHtml(reply.uuid)}"
                                  class="edit-comment-form mt-3 d-none js-ajax-form"
                                  data-action-type="update-comment"
                                  data-comment-uuid="${escapeHtml(reply.uuid)}">
                                <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                                <input type="hidden" name="_method" value="PUT">
                                <textarea name="comment" rows="2" maxlength="3000" class="form-control" required>${escapeHtml(reply.comment)}</textarea>
                                <div class="d-flex gap-2 mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm rounded-2">
                                        <i class="fas fa-check me-1"></i> Update Reply
                                    </button>
                                    <button type="button" class="btn btn-light border btn-sm rounded-2 cancel-toggle-btn"
                                            data-target="edit-form-${escapeHtml(reply.uuid)}">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
        }

        // ===== AJAX SUBMIT =====
        async function submitAjaxForm(form) {
            const actionType = form.dataset.actionType;
            const formData = new FormData(form);

            if (actionType === 'delete-comment') {
                if (!window.confirm('Delete this comment and its associated likes?')) {
                    return;
                }
            }

            setLoading(form, true);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData,
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'The request failed.');
                }

                if (actionType === 'publication-like') {
                    updatePublicationLike(data, form);
                    showMessage(data.message);
                }

                if (actionType === 'comment-like') {
                    updateCommentLike(data, form);
                    showMessage(data.message);
                }

                if (actionType === 'create-comment') {
                    commentsList.insertAdjacentHTML('afterbegin', createMainCommentHtml(data.comment));
                    form.reset();
                    if (commentsCount) {
                        commentsCount.textContent = data.comments_count;
                    }
                    showMessage(data.message);
                }

                if (actionType === 'create-reply') {
                    const parent = document.getElementById(`comment-${form.dataset.parentUuid}`);
                    const repliesContainer = parent?.querySelector('.comment-replies');
                    if (repliesContainer) {
                        repliesContainer.insertAdjacentHTML('beforeend', createReplyHtml(data.reply));
                    }
                    form.reset();
                    form.classList.add('d-none');
                    showMessage(data.message);
                }

                if (actionType === 'update-comment') {
                    const uuid = form.dataset.commentUuid;
                    const content = document.getElementById(`comment-text-${uuid}`);
                    if (content) {
                        content.textContent = data.comment;
                    }

                    // Show "Edited" label
                    const metaRow = content?.previousElementSibling;
                    if (metaRow && !metaRow.querySelector('.edited-label')) {
                        const edited = document.createElement('small');
                        edited.className = 'text-muted fst-italic edited-label';
                        edited.textContent = data.edited_label || 'Edited';
                        metaRow.appendChild(edited);
                    }

                    form.classList.add('d-none');
                    showMessage(data.message);
                }

                if (actionType === 'delete-comment') {
                    const element = document.getElementById(`comment-${form.dataset.commentUuid}`);
                    if (element) element.remove();

                    if (commentsCount) {
                        if (typeof data.comments_count !== 'undefined') {
                            commentsCount.textContent = data.comments_count;
                        } else {
                            const current = Number(commentsCount.textContent);
                            commentsCount.textContent = Math.max(current - 1, 0);
                        }
                    }
                    showMessage(data.message);
                }
            } catch (error) {
                showMessage(error.message, 'danger');
            } finally {
                setLoading(form, false);
            }
        }

        // Event listeners
        document.addEventListener('submit', function(event) {
            const form = event.target.closest('.js-ajax-form');
            if (!form) return;
            event.preventDefault();
            submitAjaxForm(form);
        });

        document.addEventListener('click', function(event) {
            const replyButton = event.target.closest('.reply-toggle-btn');
            const editButton = event.target.closest('.edit-toggle-btn');
            const cancelButton = event.target.closest('.cancel-toggle-btn');

            if (replyButton || editButton) {
                const targetId = (replyButton || editButton).dataset.target;
                const target = document.getElementById(targetId);
                if (target) {
                    target.classList.toggle('d-none');
                    if (!target.classList.contains('d-none')) {
                        target.querySelector('textarea')?.focus();
                    }
                }
            }

            if (cancelButton) {
                const target = document.getElementById(cancelButton.dataset.target);
                target?.classList.add('d-none');
            }
        });
    });
</script>

{{-- <script>
    window.publicationConfig = {
        publicationUuid: @json($data['publication']->uuid),
        commentLikeUrl: @json(route('publication.comment.like.toggle', ['commentUuid' => '__UUID__'])),
        commentUpdateUrl: @json(route('publication.comment.update', ['commentUuid' => '__UUID__'])),
        commentDeleteUrl: @json(route('publication.comment.destroy', ['commentUuid' => '__UUID__'])),
        replyStoreUrl: @json(route('publication.comment.reply.store', [
                'publicationUuid' => $data['publication']->uuid,
                'commentUuid' => '__UUID__',
            ]))
    };
</script> --}}

@php
    $publicationUuid = $data['publication']->uuid;

    $commentLikeUrlTemplate = route(
        'publication.comment.like.toggle',
        ['commentUuid' => '__UUID__']
    );

    $commentUpdateUrlTemplate = route(
        'publication.comment.update',
        ['commentUuid' => '__UUID__']
    );

    $commentDeleteUrlTemplate = route(
        'publication.comment.destroy',
        ['commentUuid' => '__UUID__']
    );

    $replyStoreUrlTemplate = route(
        'publication.comment.reply.store',
        [
            'publicationUuid' => $publicationUuid,
            'commentUuid' => '__UUID__',
        ]
    );
@endphp

<script>
    window.publicationConfig = {
        publicationUuid: @json($publicationUuid),
        commentLikeUrl: @json($commentLikeUrlTemplate),
        commentUpdateUrl: @json($commentUpdateUrlTemplate),
        commentDeleteUrl: @json($commentDeleteUrlTemplate),
        replyStoreUrl: @json($replyStoreUrlTemplate)
    };
</script>

{{-- Publication Engagement --}}
<div class="publication-engagement mt-5">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-circle-check me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-circle-exclamation me-1"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <strong><i class="fas fa-triangle-exclamation me-1"></i> Please correct the following:</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex flex-wrap align-items-center gap-3 border-top border-bottom py-3 mb-4">
        @auth
            @php
                $userLikedPublication = $data['publication']->likes->contains('username', auth()->user()->username);
            @endphp

            <form method="POST" action="{{ route('publication.like.toggle', ['uuid' => $data['publication']->uuid]) }}"
                class="d-inline js-ajax-form" data-action-type="publication-like">
                @csrf
                <button type="submit"
                    class="btn btn-sm rounded-2 publication-like-button {{ $userLikedPublication ? 'btn-danger' : 'btn-outline-danger' }}">
                    <i class="{{ $userLikedPublication ? 'fas' : 'far' }} fa-heart me-1"></i>
                    <span id="publication-likes-count">
                        {{ $data['publication']->likes_count ?? $data['publication']->likes->count() }}
                    </span>
                    <span class="ms-1 like-label">
                        {{ $userLikedPublication ? 'Unlike' : 'Like' }}
                    </span>
                </button>
            </form>
        @else
            <a href="{{ route('loginPage') }}" class="btn btn-outline-danger btn-sm rounded-2">
                <i class="far fa-heart me-1"></i>
                {{ $data['publication']->likes_count ?? $data['publication']->likes->count() }}
                <span class="ms-1">Like</span>
            </a>
        @endauth

        @php
            $commentsTotal = $data['publication']->comments_count ?? $data['publication']->mainComments->count();
        @endphp

        <span class="text-muted small">
            <i class="far fa-comments me-1"></i>
            <span id="comments-count">{{ $commentsTotal }}</span>
            {{ $commentsTotal === 1 ? 'Comment' : 'Comments' }}
        </span>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">
            <i class="fas fa-comments me-2 text-primary"></i> Comments
        </h5>
    </div>

    @auth
        <form method="POST"
            action="{{ route('publication.comment.store', ['publicationUuid' => $data['publication']->uuid]) }}"
            class="card border-0 shadow-sm rounded-3 mb-4 js-ajax-form" data-action-type="create-comment">
            @csrf
            <div class="card-body p-3 p-md-4">
                <div class="d-flex gap-3">
                    <div class="comment-avatar">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                            style="width: 42px; height: 42px; font-weight: 700;">
                            {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->username, 0, 1)) }}
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <label for="main-comment" class="form-label fw-semibold">Join the conversation</label>
                        <textarea name="comment" id="main-comment" rows="3" maxlength="3000" class="form-control"
                            placeholder="Write a comment..." required></textarea>
                        @error('comment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary btn-sm rounded-2 px-3">
                                <i class="fas fa-paper-plane me-1"></i> Post Comment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="alert alert-light border shadow-sm mb-4">
            <i class="fas fa-lock me-1"></i>
            Please <a href="{{ route('loginPage') }}" class="fw-semibold">log in</a>
            to like, comment, or reply to this publication.
        </div>
    @endauth

    <div class="comments-list" id="comments-list">
        @forelse ($data['publication']->mainComments as $comment)
            <div class="comment-item border-bottom py-4" id="comment-{{ $comment->uuid }}">
                <div class="d-flex gap-3">
                    <div class="comment-avatar">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                            style="width: 42px; height: 42px; font-weight: 700;">
                            {{ strtoupper(substr($comment->user->name ?? ($comment->username ?? 'U'), 0, 1)) }}
                        </div>
                    </div>

                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <strong class="text-dark">
                                {{ $comment->user->name ?? ($comment->username ?? 'Unknown User') }}
                            </strong>
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                            @if ($comment->edited_at)
                                <small class="text-muted fst-italic edited-label">Edited</small>
                            @endif
                        </div>

                        <p class="comment-content mt-2 mb-2" id="comment-text-{{ $comment->uuid }}">
                            {{ $comment->comment }}
                        </p>

                        <div class="d-flex flex-wrap align-items-center gap-3">
                            @auth
                                @php
                                    $userLikedComment = $comment->likes->contains('username', auth()->user()->username);
                                @endphp
                                <form method="POST"
                                    action="{{ route('publication.comment.like.toggle', ['commentUuid' => $comment->uuid]) }}"
                                    class="d-inline js-ajax-form" data-action-type="comment-like"
                                    data-comment-uuid="{{ $comment->uuid }}">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm btn-link p-0 text-decoration-none comment-like-button {{ $userLikedComment ? 'text-danger' : 'text-muted' }}">
                                        <i class="{{ $userLikedComment ? 'fas' : 'far' }} fa-heart"></i>
                                        <span class="ms-1 like-count">{{ $comment->likes->count() }}</span>
                                        <span class="ms-1 like-label">{{ $userLikedComment ? 'Unlike' : 'Like' }}</span>
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">
                                    <i class="far fa-heart me-1"></i> {{ $comment->likes->count() }}
                                </span>
                            @endauth

                            @auth
                                <button type="button"
                                    class="btn btn-sm btn-link p-0 text-decoration-none text-muted reply-toggle-btn"
                                    data-target="reply-form-{{ $comment->uuid }}">
                                    <i class="fas fa-reply me-1"></i> Reply
                                </button>
                            @endauth

                            @auth
                                @if ($comment->username === auth()->user()->username)
                                    <button type="button"
                                        class="btn btn-sm btn-link p-0 text-decoration-none text-primary edit-toggle-btn"
                                        data-target="edit-form-{{ $comment->uuid }}">
                                        <i class="fas fa-pen me-1"></i> Edit
                                    </button>

                                    <form method="POST"
                                        action="{{ route('publication.comment.destroy', ['commentUuid' => $comment->uuid]) }}"
                                        class="d-inline js-ajax-form" data-action-type="delete-comment"
                                        data-comment-uuid="{{ $comment->uuid }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm btn-link p-0 text-decoration-none text-danger">
                                            <i class="fas fa-trash-can me-1"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>

                        @auth
                            @if ($comment->username === auth()->user()->username)
                                <form method="POST"
                                    action="{{ route('publication.comment.update', ['commentUuid' => $comment->uuid]) }}"
                                    id="edit-form-{{ $comment->uuid }}"
                                    class="edit-comment-form mt-3 d-none js-ajax-form" data-action-type="update-comment"
                                    data-comment-uuid="{{ $comment->uuid }}">
                                    @csrf
                                    @method('PUT')
                                    <textarea name="comment" rows="3" maxlength="3000" class="form-control" required>{{ $comment->comment }}</textarea>
                                    <div class="d-flex gap-2 mt-2">
                                        <button type="submit" class="btn btn-primary btn-sm rounded-2">
                                            <i class="fas fa-check me-1"></i> Update Comment
                                        </button>
                                        <button type="button"
                                            class="btn btn-light border btn-sm rounded-2 cancel-toggle-btn"
                                            data-target="edit-form-{{ $comment->uuid }}">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            @endif
                        @endauth

                        @auth
                            <form method="POST"
                                action="{{ route('publication.comment.reply.store', ['publicationUuid' => $data['publication']->uuid, 'commentUuid' => $comment->uuid]) }}"
                                id="reply-form-{{ $comment->uuid }}" class="reply-form mt-3 d-none js-ajax-form"
                                data-action-type="create-reply" data-parent-uuid="{{ $comment->uuid }}">
                                @csrf
                                <textarea name="comment" rows="2" maxlength="3000" class="form-control"
                                    placeholder="Reply to {{ $comment->user->name ?? ($comment->username ?? 'this comment') }}..." required></textarea>
                                <div class="d-flex gap-2 mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm rounded-2">
                                        <i class="fas fa-paper-plane me-1"></i> Reply
                                    </button>
                                    <button type="button" class="btn btn-light border btn-sm rounded-2 cancel-toggle-btn"
                                        data-target="reply-form-{{ $comment->uuid }}">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        @endauth

                        @if ($comment->replies->count() > 0)
                            <div class="comment-replies mt-3">
                                @foreach ($comment->replies as $reply)
                                    <div class="comment-reply ms-md-4 mt-3 ps-3 border-start"
                                        id="comment-{{ $reply->uuid }}">
                                        <div class="d-flex gap-2">
                                            <div class="rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center flex-shrink-0"
                                                style="width: 34px; height: 34px; font-size: 13px; font-weight: 700;">
                                                {{ strtoupper(substr($reply->user->name ?? ($reply->username ?? 'U'), 0, 1)) }}
                                            </div>

                                            <div class="flex-grow-1 overflow-hidden">
                                                <div class="d-flex flex-wrap align-items-center gap-2">
                                                    <strong class="small text-dark">
                                                        {{ $reply->user->name ?? ($reply->username ?? 'Unknown User') }}
                                                    </strong>
                                                    <small
                                                        class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                                    @if ($reply->edited_at)
                                                        <small
                                                            class="text-muted fst-italic edited-label">Edited</small>
                                                    @endif
                                                </div>

                                                <p class="comment-content mb-2 mt-1"
                                                    id="comment-text-{{ $reply->uuid }}">
                                                    {{ $reply->comment }}
                                                </p>

                                                <div class="d-flex flex-wrap align-items-center gap-3">
                                                    @auth
                                                        @php
                                                            $userLikedReply = $reply->likes->contains(
                                                                'username',
                                                                auth()->user()->username,
                                                            );
                                                        @endphp
                                                        <form method="POST"
                                                            action="{{ route('publication.comment.like.toggle', ['commentUuid' => $reply->uuid]) }}"
                                                            class="d-inline js-ajax-form" data-action-type="comment-like"
                                                            data-comment-uuid="{{ $reply->uuid }}">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-sm btn-link p-0 text-decoration-none {{ $userLikedReply ? 'text-danger' : 'text-muted' }}">
                                                                <i
                                                                    class="{{ $userLikedReply ? 'fas' : 'far' }} fa-heart"></i>
                                                                <span
                                                                    class="ms-1 like-count">{{ $reply->likes->count() }}</span>
                                                                <span
                                                                    class="ms-1 like-label">{{ $userLikedReply ? 'Unlike' : 'Like' }}</span>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted small">
                                                            <i class="far fa-heart me-1"></i> {{ $reply->likes->count() }}
                                                        </span>
                                                    @endauth

                                                    @auth
                                                        @if ($reply->username === auth()->user()->username)
                                                            <button type="button"
                                                                class="btn btn-sm btn-link p-0 text-decoration-none text-primary edit-toggle-btn"
                                                                data-target="edit-form-{{ $reply->uuid }}">
                                                                <i class="fas fa-pen me-1"></i> Edit
                                                            </button>

                                                            <form method="POST"
                                                                action="{{ route('publication.comment.destroy', ['commentUuid' => $reply->uuid]) }}"
                                                                class="d-inline js-ajax-form"
                                                                data-action-type="delete-comment"
                                                                data-comment-uuid="{{ $reply->uuid }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-link p-0 text-decoration-none text-danger">
                                                                    <i class="fas fa-trash-can me-1"></i> Delete
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endauth
                                                </div>

                                                @auth
                                                    @if ($reply->username === auth()->user()->username)
                                                        <form method="POST"
                                                            action="{{ route('publication.comment.update', ['commentUuid' => $reply->uuid]) }}"
                                                            id="edit-form-{{ $reply->uuid }}"
                                                            class="edit-comment-form mt-3 d-none js-ajax-form"
                                                            data-action-type="update-comment"
                                                            data-comment-uuid="{{ $reply->uuid }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <textarea name="comment" rows="2" maxlength="3000" class="form-control" required>{{ $reply->comment }}</textarea>
                                                            <div class="d-flex gap-2 mt-2">
                                                                <button type="submit"
                                                                    class="btn btn-primary btn-sm rounded-2">
                                                                    <i class="fas fa-check me-1"></i> Update Reply
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-light border btn-sm rounded-2 cancel-toggle-btn"
                                                                    data-target="edit-form-{{ $reply->uuid }}">
                                                                    Cancel
                                                                </button>
                                                            </div>
                                                        </form>
                                                    @endif
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center border rounded-3 py-5 px-3">
                <i class="far fa-comments fa-2x text-muted mb-3"></i>
                <p class="text-muted mb-0">No comments yet. Be the first to share your thoughts.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    .publication-engagement {
        /* removed max-width so it uses full available width */
    }

    .comment-content {
        color: #343a40;
        line-height: 1.65;
        overflow-wrap: anywhere;
        /* white-space: pre-wrap; */
        white-space: pre-line;
    }

    .comment-item:last-child {
        border-bottom: 0 !important;
    }

    .comment-reply {
        border-radius: 0 10px 10px 0;
        background: rgba(0, 0, 0, 0.018);
        padding: 12px;
    }

    .reply-form,
    .edit-comment-form {
        max-width: 700px;
    }

    .btn-link {
        box-shadow: none !important;
    }

    .btn-link:hover {
        opacity: 0.82;
    }
</style>
