import { createRouter, createWebHistory } from 'vue-router'
import Home from '../Pages/Home.vue';
import CreatePost from '../Pages/CreatePost.vue';
import Posts from '../Pages/Posts.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/post',
        name: 'post',
        component: Posts
    },
    // {
    //     path: '/post/:id(\\d{5})',
    //     name: 'post.view',
    //     component: About
    // },
    {
        path: '/post/create',
        name: 'post.create',
        component: CreatePost
    }
]

const router = createRouter({
    // history: createWebHistory(process.env.BASE_URL),
    history: createWebHistory(),
    routes
})
export default router