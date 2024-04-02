<script setup>
import Navbar from "./Navbar.vue";


</script>

<template>

  <Navbar />

<!--  <div id="nav">-->
<!--    <router-link to="/">Home</router-link> |-->
<!--    <router-link to="/about">About</router-link>-->
<!--  </div>-->

  <div class="container-fluid">

    <router-view/>
  </div>

</template>

<style scoped>
  #nav{
    background-color: blue;
  }
</style>