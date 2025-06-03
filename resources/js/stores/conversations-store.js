// resources/js/stores/conversations-store.js

document.addEventListener("alpine:init", () => {
    Alpine.store("conversationsUnreadCounts", {
        counts: {}, // Object to store counts: { conversation_id: unread_count }

        // Initialize counts from the DOM
        init() {
            // This part is crucial: we need to get the initial counts from the table
            // However, Alpine stores are usually initialized before DOM is fully parsed for x-data
            // So, a better approach is to pass initial data from PHP to JavaScript
            // This init method might be empty or used for general setup
        },

        setCount(conversationId, count) {
            this.counts[conversationId] = count;
        },

        incrementUnread(conversationId) {
            if (this.counts[conversationId] === undefined) {
                // If not yet initialized, assume 0 or fetch initial state if needed
                this.counts[conversationId] = 1;
            } else {
                this.counts[conversationId]++;
            }
        },

        decrementUnread(conversationId) {
            if (this.counts[conversationId] > 0) {
                this.counts[conversationId]--;
            }
        },

        resetUnread(conversationId) {
            this.counts[conversationId] = 0;
        },
    });
});
