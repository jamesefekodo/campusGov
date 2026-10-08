@php
    $pastQuestion = $data['pastQuestion'];

    $contributions = $pastQuestion->contributions ?? collect();
@endphp

<div class="past-question-contributions mt-5">

    <div id="pastQuestionContributionMessage" class="alert d-none" role="alert">
    </div>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">
            <i class="fas fa-lightbulb text-info me-2"></i>
            Contributions

            <span id="contributionsCount" class="badge bg-secondary ms-1">

                {{ $pastQuestion->contributions_count ?? $contributions->count() }}
            </span>
        </h4>
    </div>

    @auth
        <form method="POST"
            action="{{ route('pastQuestion.contribution.store', [
                'pastQuestion' => $pastQuestion->uuid,
            ]) }}"
            id="createContributionForm" class="card border-0 shadow-sm mb-4 js-contribution-form">

            @csrf

            <div class="card-body">
                <label for="newContribution" class="form-label fw-semibold">

                    Add a contribution
                </label>

                <textarea name="content" id="newContribution" class="form-control contribution-editor" rows="7" maxlength="30000"
                    placeholder="Share a correction, explanation, answer, reference or useful note..." required></textarea>

                @error('content')
                    <div class="text-danger mt-1">
                        {{ $message }}
                    </div>
                @enderror

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary">

                        <i class="fas fa-paper-plane me-1"></i>
                        Add Contribution
                    </button>
                </div>
            </div>
        </form>
    @else
        <div class="alert alert-light border">
            <i class="fas fa-lock me-1"></i>

            Please
            <a href="{{ route('loginPage') }}" class="fw-semibold">

                log in
            </a>
            to add or like a contribution.
        </div>
    @endauth

    <div id="contributionsList">

        @forelse ($contributions as $contribution)
            @php
                $isOwner = Auth::check() && Auth::user()->username === $contribution->username;

                $isLiked =
                    Auth::check() && $contribution->likes->where('username', Auth::user()->username)->isNotEmpty();

                $creatorName = $contribution->creator_name;
            @endphp

            <article class="contribution-item border-bottom py-4" id="contribution-{{ $contribution->uuid }}">

                <div class="d-flex gap-3">

                    <div class="contribution-avatar">
                        {{ strtoupper(substr($creatorName ?: 'U', 0, 1)) }}
                    </div>

                    <div class="flex-grow-1">

                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <strong class="text-dark">
                                {{ $creatorName }}
                            </strong>

                            <small class="text-muted">
                                {{ $contribution->created_at?->diffForHumans() }}
                            </small>

                            @if ($contribution->edited_at)
                                <small class="text-muted fst-italic edited-label">
                                    Edited
                                </small>
                            @endif
                        </div>

                        <div class="contribution-content mt-2 mb-2" id="contribution-text-{{ $contribution->uuid }}">

                            {!! $contribution->content !!}
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-3">

                            @auth
                                <form method="POST"
                                    action="{{ route('pastQuestion.contribution.like.toggle', [
                                        'contribution' => $contribution->uuid,
                                    ]) }}"
                                    class="d-inline js-contribution-like-form"
                                    data-contribution-uuid="{{ $contribution->uuid }}">

                                    @csrf

                                    <button type="submit"
                                        class="btn btn-sm btn-link p-0 text-decoration-none {{ $isLiked ? 'text-danger' : 'text-muted' }}">

                                        <i class="{{ $isLiked ? 'fas' : 'far' }} fa-heart"></i>

                                        <span class="like-count">
                                            {{ $contribution->likes_count ?? $contribution->likes->count() }}
                                        </span>

                                        <span class="like-label">
                                            {{ $isLiked ? 'Unlike' : 'Like' }}
                                        </span>
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">
                                    <i class="far fa-heart"></i>

                                    {{ $contribution->likes_count ?? $contribution->likes->count() }}
                                    Likes
                                </span>
                            @endauth

                            @if ($isOwner)
                                <button type="button"
                                    class="btn btn-sm btn-link p-0 text-primary edit-contribution-button"
                                    data-target="edit-contribution-{{ $contribution->uuid }}">

                                    <i class="fas fa-pen me-1"></i>
                                    Edit
                                </button>

                                <form method="POST"
                                    action="{{ route('pastQuestion.contribution.destroy', [
                                        'contribution' => $contribution->uuid,
                                    ]) }}"
                                    class="d-inline js-delete-contribution-form"
                                    data-contribution-uuid="{{ $contribution->uuid }}">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-link p-0 text-danger">

                                        <i class="fas fa-trash me-1"></i>
                                        Delete
                                    </button>
                                </form>
                            @endif

                        </div>

                        @if ($isOwner)
                            <form method="POST"
                                action="{{ route('pastQuestion.contribution.update', [
                                    'contribution' => $contribution->uuid,
                                ]) }}"
                                id="edit-contribution-{{ $contribution->uuid }}"
                                class="edit-contribution-form mt-3 d-none">

                                @csrf
                                @method('PUT')

                                <textarea name="content" rows="7" maxlength="30000" class="form-control contribution-editor" required>{!! $contribution->content !!}</textarea>

                                <div class="d-flex gap-2 mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm">

                                        <i class="fas fa-check me-1"></i>
                                        Update
                                    </button>

                                    <button type="button" class="btn btn-light border btn-sm cancel-contribution-edit"
                                        data-target="edit-contribution-{{ $contribution->uuid }}">

                                        Cancel
                                    </button>
                                </div>
                            </form>
                        @endif

                    </div>
                </div>
            </article>
        @empty
            <div id="emptyContributionsMessage" class="text-center border rounded-3 py-5 px-3">

                <i class="far fa-lightbulb fa-2x text-muted mb-3"></i>

                <p class="text-muted mb-0">
                    No contributions yet. Be the first to share something useful, like solutions, additions,
                    corrections, and so on.
                </p>
            </div>
        @endforelse

    </div>
</div>

<style>
    .past-question-contributions {
        max-width: 100%;
    }

    .contribution-item:last-child {
        border-bottom: 0 !important;
    }

    .contribution-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #e7f1ff;
        color: #0d6efd;
        font-weight: 700;
    }

    .contribution-content {
        color: #343a40;
        line-height: 1.75;
        overflow-wrap: anywhere;
    }

    .contribution-content p {
        margin-bottom: .75rem;
    }

    .contribution-content ul,
    .contribution-content ol {
        padding-left: 1.5rem;
        margin-bottom: .85rem;
    }

    .contribution-content blockquote {
        padding: .75rem 1rem;
        margin: 1rem 0;
        border-left: 4px solid #0d6efd;
        background: #f5f8fc;
        color: #495057;
    }

    .contribution-content table {
        width: 100%;
        margin: 1rem 0;
        border-collapse: collapse;
    }

    .contribution-content th,
    .contribution-content td {
        padding: .5rem;
        border: 1px solid #dee2e6;
    }

    .contribution-content img {
        max-width: 100%;
        height: auto;
    }

    .contribution-item .btn-link {
        box-shadow: none !important;
        text-decoration: none;
    }

    .contribution-item .btn-link:hover {
        opacity: .78;
    }

    .edit-contribution-form {
        max-width: 850px;
    }

    .edit-contribution-form .note-editor,
    #createContributionForm .note-editor {
        margin-bottom: 8px;
    }

    .note-editor .note-editable {
        min-height: 180px;
        line-height: 1.65;
    }

    @media (max-width: 767px) {
        .contribution-avatar {
            flex-basis: 36px;
            width: 36px;
            height: 36px;
        }

        .note-editor .note-editable {
            min-height: 150px;
        }
    }
</style>

<script>
    function contributionSummernoteOptions() {
        return {
            height: 280,
            minHeight: 180,
            maxHeight: 650,
            dialogsInBody: true,

            toolbar: [
                ['style', ['style']],

                ['font', [
                    'bold',
                    'italic',
                    'underline',
                    'clear'
                ]],

                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],

                ['para', [
                    'ul',
                    'ol',
                    'paragraph'
                ]],

                ['table', ['table']],
                ['insert', ['link']],

                ['view', [
                    'fullscreen',
                    'codeview',
                    'help'
                ]]
            ]
        };
    }

    function initialiseContributionEditor(element) {
        const editor = $(element);

        if (!editor.length) {
            return;
        }

        if (editor.next('.note-editor').length) {
            return;
        }

        editor.summernote(
            contributionSummernoteOptions()
        );
    }

    $(document).ready(function() {
        initialiseContributionEditor('#newContribution');

        $('.edit-contribution-form .contribution-editor')
            .each(function() {
                initialiseContributionEditor(this);
            });
    });
</script>


<script>
    window.pastQuestionContributionConfig = {
        updateUrl: @json(route('pastQuestion.contribution.update', ['contribution' => '__UUID__'])),

        deleteUrl: @json(route('pastQuestion.contribution.destroy', ['contribution' => '__UUID__'])),

        likeUrl: @json(route('pastQuestion.contribution.like.toggle', ['contribution' => '__UUID__']))
    };
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');

        const contributionsList = document.getElementById(
            'contributionsList'
        );

        const contributionsCount = document.getElementById(
            'contributionsCount'
        );

        const messageBox = document.getElementById(
            'pastQuestionContributionMessage'
        );

        function escapeHtml(value) {
            const div = document.createElement('div');

            div.textContent = value ?? '';

            return div.innerHTML;
        }

        function showMessage(message, type = 'success') {
            if (!messageBox) {
                return;
            }

            messageBox.className =
                'alert alert-' + type;

            messageBox.textContent = message;

            messageBox.classList.remove('d-none');

            setTimeout(function() {
                messageBox.classList.add('d-none');
            }, 4000);
        }

        function setFormLoading(form, loading) {
            const button = form.querySelector(
                'button[type="submit"]'
            );

            if (!button) {
                return;
            }

            if (loading) {
                button.dataset.originalHtml = button.innerHTML;
                button.disabled = true;

                button.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1"></span>' +
                    'Please wait...';
            } else {
                button.disabled = false;

                if (button.dataset.originalHtml) {
                    button.innerHTML =
                        button.dataset.originalHtml;

                    delete button.dataset.originalHtml;
                }
            }
        }

        function commitSummernote(form) {
            $(form)
                .find('.contribution-editor')
                .each(function() {
                    if ($(this).next('.note-editor').length) {
                        $(this).summernote('commit');
                    }
                });
        }

        function updateContributionCount(value) {
            if (contributionsCount && value !== undefined) {
                contributionsCount.textContent = value;
            }
        }

        function buildUrl(template, uuid) {
            return template.replace(
                '__UUID__',
                encodeURIComponent(uuid)
            );
        }

        function initialiseEditEditor(form) {
            const editor = form.querySelector(
                '.contribution-editor'
            );

            if (!editor) {
                return;
            }

            initialiseContributionEditor(editor);
        }

        function createContributionHtml(contribution) {
            const updateUrl = buildUrl(
                window.pastQuestionContributionConfig.updateUrl,
                contribution.uuid
            );

            const deleteUrl = buildUrl(
                window.pastQuestionContributionConfig.deleteUrl,
                contribution.uuid
            );

            const likeUrl = buildUrl(
                window.pastQuestionContributionConfig.likeUrl,
                contribution.uuid
            );

            const ownerControls = contribution.is_owner ?
                `
                    <button
                        type="button"
                        class="btn btn-sm btn-link p-0 text-primary edit-contribution-button"
                        data-target="edit-contribution-${escapeHtml(contribution.uuid)}">

                        <i class="fas fa-pen me-1"></i>
                        Edit
                    </button>

                    <form
                        method="POST"
                        action="${deleteUrl}"
                        class="d-inline js-delete-contribution-form"
                        data-contribution-uuid="${escapeHtml(contribution.uuid)}">

                        <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                        <input type="hidden" name="_method" value="DELETE">

                        <button
                            type="submit"
                            class="btn btn-sm btn-link p-0 text-danger">

                            <i class="fas fa-trash me-1"></i>
                            Delete
                        </button>
                    </form>
                ` :
                '';

            const editForm = contribution.is_owner ?
                `
                    <form
                        method="POST"
                        action="${updateUrl}"
                        id="edit-contribution-${escapeHtml(contribution.uuid)}"
                        class="edit-contribution-form mt-3 d-none">

                        <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                        <input type="hidden" name="_method" value="PUT">

                        <textarea
                            name="content"
                            rows="7"
                            maxlength="30000"
                            class="form-control contribution-editor"
                            required>${contribution.content}</textarea>

                        <div class="d-flex gap-2 mt-2">
                            <button
                                type="submit"
                                class="btn btn-primary btn-sm">

                                <i class="fas fa-check me-1"></i>
                                Update
                            </button>

                            <button
                                type="button"
                                class="btn btn-light border btn-sm cancel-contribution-edit"
                                data-target="edit-contribution-${escapeHtml(contribution.uuid)}">

                                Cancel
                            </button>
                        </div>
                    </form>
                ` :
                '';

            return `
                <article
                    class="contribution-item border-bottom py-4"
                    id="contribution-${escapeHtml(contribution.uuid)}">

                    <div class="d-flex gap-3">
                        <div class="contribution-avatar">
                            ${escapeHtml(
                                (contribution.creator_name || 'U')
                                    .charAt(0)
                                    .toUpperCase()
                            )}
                        </div>

                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <strong class="text-dark">
                                    ${escapeHtml(contribution.creator_name)}
                                </strong>

                                <small class="text-muted">
                                    ${escapeHtml(contribution.created_at)}
                                </small>
                            </div>

                            <div
                                class="contribution-content mt-2 mb-2"
                                id="contribution-text-${escapeHtml(contribution.uuid)}">

                                ${contribution.content}
                            </div>

                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <form
                                    method="POST"
                                    action="${likeUrl}"
                                    class="d-inline js-contribution-like-form"
                                    data-contribution-uuid="${escapeHtml(contribution.uuid)}">

                                    <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-link p-0 text-decoration-none ${contribution.liked ? 'text-danger' : 'text-muted'}">

                                        <i class="${contribution.liked ? 'fas' : 'far'} fa-heart"></i>

                                        <span class="like-count">
                                            ${contribution.likes_count}
                                        </span>

                                        <span class="like-label">
                                            ${contribution.liked ? 'Unlike' : 'Like'}
                                        </span>
                                    </button>
                                </form>

                                ${ownerControls}
                            </div>

                            ${editForm}
                        </div>
                    </div>
                </article>
            `;
        }

        async function submitAjaxForm(form) {
            commitSummernote(form);

            const formData = new FormData(form);

            setFormLoading(form, true);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(
                        data.message || 'The request failed.'
                    );
                }

                if (form.id === 'createContributionForm') {
                    const emptyMessage = document.getElementById(
                        'emptyContributionsMessage'
                    );

                    if (emptyMessage) {
                        emptyMessage.remove();
                    }

                    contributionsList.insertAdjacentHTML(
                        'afterbegin',
                        createContributionHtml(data.contribution)
                    );

                    const insertedContribution =
                        document.getElementById(
                            'contribution-' +
                            data.contribution.uuid
                        );

                    if (insertedContribution) {
                        initialiseEditEditor(
                            insertedContribution
                        );
                    }

                    form.reset();

                    $('#newContribution').summernote('reset');

                    updateContributionCount(
                        data.contributions_count
                    );

                    showMessage(data.message);

                    return;
                }

                if (
                    form.classList.contains(
                        'js-contribution-like-form'
                    )
                ) {
                    const button = form.querySelector(
                        'button[type="submit"]'
                    );

                    const icon = button.querySelector('i');
                    const count = button.querySelector('.like-count');
                    const label = button.querySelector('.like-label');

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

                    showMessage(data.message);

                    return;
                }

                if (
                    form.classList.contains(
                        'js-delete-contribution-form'
                    )
                ) {
                    const uuid = form.dataset.contributionUuid;

                    document
                        .getElementById('contribution-' + uuid)
                        ?.remove();

                    updateContributionCount(
                        data.contributions_count
                    );

                    if (
                        !contributionsList.querySelector(
                            '.contribution-item'
                        )
                    ) {
                        contributionsList.innerHTML = `
                            <div
                                id="emptyContributionsMessage"
                                class="text-center border rounded-3 py-5 px-3">

                                <i class="far fa-lightbulb fa-2x text-muted mb-3"></i>

                                <p class="text-muted mb-0">
                                    No contributions yet.
                                </p>
                            </div>
                        `;
                    }

                    showMessage(data.message);

                    return;
                }

                if (
                    form.classList.contains(
                        'edit-contribution-form'
                    )
                ) {
                    const uuid = form.id.replace(
                        'edit-contribution-',
                        ''
                    );

                    const contributionText = document.getElementById(
                        'contribution-text-' + uuid
                    );

                    if (contributionText) {
                        contributionText.innerHTML =
                            data.contribution.content;
                    }

                    const header =
                        document.getElementById(
                            'contribution-' + uuid
                        );

                    if (header && !header.querySelector('.edited-label')) {
                        const editedLabel =
                            document.createElement('small');

                        editedLabel.className =
                            'text-muted fst-italic edited-label';

                        editedLabel.textContent = 'Edited';

                        const meta =
                            header.querySelector(
                                '.d-flex.flex-wrap.align-items-center.gap-2'
                            );

                        meta?.appendChild(editedLabel);
                    }

                    form.classList.add('d-none');

                    showMessage(data.message);
                }
            } catch (error) {
                showMessage(error.message, 'danger');
            } finally {
                setFormLoading(form, false);
            }
        }

        document.addEventListener('submit', function(event) {
            const form = event.target.closest(
                '#createContributionForm, ' +
                '.js-contribution-like-form, ' +
                '.js-delete-contribution-form, ' +
                '.edit-contribution-form'
            );

            if (!form) {
                return;
            }

            event.preventDefault();

            if (
                form.classList.contains(
                    'js-delete-contribution-form'
                )
            ) {
                if (!confirm(
                        'Delete this contribution permanently?'
                    )) {
                    return;
                }
            }

            submitAjaxForm(form);
        });

        document.addEventListener('click', function(event) {
            const editButton = event.target.closest(
                '.edit-contribution-button'
            );

            const cancelButton = event.target.closest(
                '.cancel-contribution-edit'
            );

            if (editButton) {
                const target = document.getElementById(
                    editButton.dataset.target
                );

                if (target) {
                    target.classList.toggle('d-none');

                    if (!target.classList.contains('d-none')) {
                        initialiseEditEditor(target);

                        setTimeout(function() {
                            $(target)
                                .find('.contribution-editor')
                                .summernote('focus');
                        }, 100);
                    }
                }
            }

            if (cancelButton) {
                const target = document.getElementById(
                    cancelButton.dataset.target
                );

                if (target) {
                    target.classList.add('d-none');
                }
            }
        });
    });
</script>
