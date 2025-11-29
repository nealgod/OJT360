---
description: Refactor messaging system to conversation-based UI
---

# Goal
Transform the current individual message list into a conversation-based (chat-style) system while preserving role-based permissions.

# Steps

1.  **Update Routes**
    - Add `Route::get('/messages/chat/{user}', [MessageController::class, 'chat'])->name('messages.chat');` to `routes/web.php`.

2.  **Update MessageController**
    - **Refactor `index`**:
        - Fetch all messages involving the user.
        - Group by the "other" user ID.
        - Select the latest message for each group.
        - Calculate unread counts per conversation.
        - Return a list of "conversations".
    - **Add `chat(User $user)`**:
        - Check `canSendMessageTo` (or if they have existing history).
        - Fetch all messages between `Auth::id()` and `$user->id`, ordered by `created_at ASC`.
        - Mark unread messages from `$user` as read.
        - Return `messages.chat` view.
    - **Update `store`**:
        - Redirect to `route('messages.chat', $request->recipient_id)`.

3.  **Create/Update Views**
    - **`resources/views/messages/index.blade.php`**:
        - Loop through "conversations".
        - Display Contact Avatar, Name, Role, Last Message snippet, Timestamp.
        - Link to `messages.chat`.
    - **`resources/views/messages/chat.blade.php`**:
        - Display header with Contact info.
        - Loop through messages:
            - If sender == me: Right aligned bubble.
            - If sender == them: Left aligned bubble.
        - Add form at bottom: Textarea + Send button.
    - **`resources/views/messages/create.blade.php`**:
        - Keep the recipient selector.
        - On submit (or change), redirect to the chat view for that user.

4.  **Verify Permissions**
    - Ensure `canSendMessageTo` is still strictly enforced in the new `chat` method and `store` method.
