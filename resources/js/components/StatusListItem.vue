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
        <div class="card-footer">
            <comment-list
                :comments="status.comments"
                :status-id="status.id"
            ></comment-list>
            <form @submit.prevent="addComment" v-if="isAuthenticated">
                <div class="d-flex align-items-center">
                    <img
                        :src="currentUser.avatar"
                        :alt="currentUser.name" width="34px" class="shadow-sm mr-2">
                    <div class="input-group">
                        <textarea
                            v-model="newComment"
                            class="form-control border-0 shadow-sm"
                            name="comment"
                            placeholder="Escribe un comentario"
                            rows="1"
                            required
                        ></textarea>
                        <div class="input-group-append">
                            <button dusk="comment-btn" class="btn btn-primary">Enviar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import LikeBtn from "./LikeBtn";
import CommentList from "./CommentList";

export default {
    props: {
        status: {
            type: Object,
            required: true,
        },
    },
    components: { LikeBtn, CommentList },
    data() {
        return {
            newComment: '',
        }
    },
    methods: {
        addComment() {
            axios.post(`/statuses/${this.status.id}/comments`, { body: this.newComment })
                .then(res => {
                    this.newComment = ''
                    EventBus.$emit(`statuses.${this.status.id}.comments`, res.data.data)
                })
                .catch(err => {
                    console.log(err.response.data)
                })
        },
    }
}
</script>

<style scoped>

</style>
