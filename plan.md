- users
- 
- workspaces (crud, user_id)
- channels (crud, workspace_id)
- messages (crud, user_id, channel_id)
- media (crud, message_id)
- reactions (crud, message_id, user_id)

// dont allow to delete the last channel of the workspace
// -- remove empty states.
