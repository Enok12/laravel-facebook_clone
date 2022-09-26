<template>
  <div class="flex flex-col flex-1 h-screen overflow-y-hidden">
    <Nav />

    <div class="flex overflow-y-hidden flex-1">
      <Sidebar />

      <div class="overflow-x-hidden w-2/3">
        <router-view></router-view>
        <!-- <p v-for="i in 100">Testing</p> -->
      </div>
    </div>
  </div>
</template>

<script>
import Nav from "./Nav.vue";
import Sidebar from "./Sidebar.vue";

export default {
  name: "App",
  components: {
    Nav,
    Sidebar,
  },
};
</script>

<style scoped>
</style>
