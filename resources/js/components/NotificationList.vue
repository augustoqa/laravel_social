<template>
    <li class="nav-item dropdown">
        <a
            dusk="notifications"
            class="nav-link dropdown-toggle"
            :class="count ? 'text-primary font-weight-bold' : ''"
            href="#"
            role="button"
            data-toggle="dropdown"
            aria-expanded="false"
        >
            <slot></slot> <span dusk="notifications-count">{{ count }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <div class="dropdown-header text-center">Notificaciones</div>
            <notification-list-item v-for="notification in notifications"
                                    :notification="notification"
                                    :key="notification.id"
            ></notification-list-item>
        </div>
    </li>
</template>

<script>
import NotificationListItem from './NotificationListItem'

export default {
    components: {
        NotificationListItem,
    },
    data() {
        return {
            notifications: [],
            count: '',
        }
    },
    created() {
        // Channel: private-App.User.6,
        // Event: Illuminate\Notifications\Events\BroadcastNotificationCreated
        if (this.isAuthenticated)
        {
            Echo.private(`App.User.${this.currentUser.id}`)
                .notification(notification => {
                    this.count++
                    this.notifications.push({
                        id: notification.id,
                        data: {
                            link: notification.link,
                            message: notification.message,
                        }
                    })
                })
        }

        axios.get('/notifications')
            .then(res => {
                this.notifications = res.data
                this.unreadNotifications()
            })

        EventBus.$on('notification-read', () => {
            if (this.count === 1) {
                return this.count = ''
            }
            this.count--
        })
        EventBus.$on('notification-unread', () => {
            this.count++
        })
    },
    methods: {
        unreadNotifications() {
            this.count = this.notifications.filter(notification => notification.read_at === null).length || ''
        }
    }
}
</script>
