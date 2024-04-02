<script setup>
import { useRouter, useRoute } from 'vue-router';

const route = useRoute();
const router = useRouter();

// console.log(router.options.routes)

const navigation = [
  {
    title: "Home",
    url: "/",
    name: 'home',
    active: router.currentRoute.value.name === 'home',
  },
  {
    title: "Posts",
    url: "/post",
    name: 'post',
    active: router.currentRoute.value.name === 'post' || router.currentRoute.value.name === 'post.view',
  },
  {
    title: "Create Post",
    url: "/post/create",
    name: 'post.create',
    active: router.currentRoute.value.name === 'post.create',
  }
];

// console.log(navigation)

</script>

<template>

<!--  {{navigation}}-->
  <nav class="navbar navbar-expand-lg bg-light">
    <div class="container-fluid">
<!--      <a class="navbar-brand" href="#">Navbar</a>-->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li v-for="link in navigation" class="nav-item">
            <router-link class="nav-link" :class="{
              active: route.name === link.name
            }" :to="link.url">{{link.title}}</router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>

</template>

<style scoped>
</style>