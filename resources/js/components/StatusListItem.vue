<template>
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body d-flex flex-column">
            <div class="d-flex align-items-center mb-3">
                <img width="40" :src="status.user.avatar" :alt="status.user.name" class="mr-3">
                <div>
                    <h5 class="mb-1"><a :href="status.user.link" v-text="status.user.name"></a> </h5>
                    <div class="small text-muted" v-text="status.ago"></div>
                </div>
            </div>
            <p v-text="status.body" class="card-text text-secondary"></p>
        </div>
        <div class="card-footer p-2 d-flex justify-content-between align-items-center">

            <like-btn
                :model="status"
                :url="`/statuses/${status.id}/likes`"
                dusk="like-btn"
            ></like-btn>

            <div class="text-secondary mr-2">
                <i class="fa-regular fa-thumbs-up"></i>
                <span dusk="likes-count">{{ status.likes_count }}</span>
            </div>
        </div>
        <div v-if="isAuthenticated || status.comments.length" class="card-footer pb-0">
            <comment-list
                :comments="status.comments"
                :status-id="status.id"
            ></comment-list>
            <comment-form :status-id="status.id"></comment-form>
        </div>
    </div>
</template>

<script>
import LikeBtn from "./LikeBtn";
import CommentList from "./CommentList";
import CommentForm from "./CommentForm";

export default {
    props: {
        status: {
            type: Object,
            required: true,
        },
    },
    components: { LikeBtn, CommentList, CommentForm },
    mounted() {
        Echo.channel(`statuses.${this.status.id}.likes`)
            .listen('ModelLiked', e => {
                this.status.likes_count++
            })

        Echo.channel(`statuses.${this.status.id}.likes`)
            .listen('ModelUnliked', e => {
                this.status.likes_count--
            })
    }
}
</script>

<style scoped>

</style>
