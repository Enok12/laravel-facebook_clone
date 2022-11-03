<template>
  <div class="flex flex-col items-center" v-if="status.user === 'Success' && user">
    <div class="relative mb-8">
      <div class="w-100 h-64 overflow-hidden z-10">
        <UploadableImage image-width='1200' image-height='800' location='cover' :user-image="user.data.attributes.cover_image"
        classes="object-cover w-full" alt="Cover" />
      </div>

      <div class="flex items-center absolute bottom-0 left-0 mb-1 z-20 ml-12">
        <div class="w-32">
          <UploadableImage image-width='750' image-height='750' location='profile' :user-image="user.data.attributes.profile_image"
        classes="object-cover w-32 h-32 border-4 border-gray-200 rounded-full" alt="Profile" />

          
        </div>
        <p class="ml-4 text-2xl text-gray-100">
          {{ user.data.attributes.name }}
        </p>
      </div>
      <div class="absolute flex items-center bottom-0 right-0 mb-4 z-20 mr-12">
        <button v-if="FriendButtontext && FriendButtontext !== 'Accept' "
         class="py-1 px-3 bg-gray-200 rounded" 
        @click="$store.dispatch('sendFriendRequest',$route.params.userId)">
          {{ FriendButtontext }}
          </button>

          <button v-if="FriendButtontext && FriendButtontext === 'Accept' "
         class="mr-2 py-1 px-3 bg-blue-500 rounded" 
        @click="$store.dispatch('acceptFriendRequest',$route.params.userId)">
         Accept
          </button>

           <button v-if="FriendButtontext && FriendButtontext === 'Accept' "
         class="py-1 px-3 bg-gray-400 rounded" 
        @click="$store.dispatch('ignoreFriendRequest',$route.params.userId)">
         Ignore
          </button>

      </div>
    </div>

    <p v-if="status.posts === 'Loading'">Loading Posts......</p>

    <p v-else-if="posts.length < 1">Get Stareted</p>

    <Post
      v-else
      v-for="(post,postKey) in posts.data"
      :key="postKey"
      :post="post"
    />

  </div>
</template>

<script>
import Post from "../Post.vue";
import {mapGetters} from 'vuex'
import UploadableImage from "../UploadableImage"



export default {
  name: "Show",

  components: {
    Post,
    UploadableImage,
  },
  mounted() {

   this.$store.dispatch('fetchUser',this.$route.params.userId)
   this.$store.dispatch('fetchUserPosts',this.$route.params.userId)


    
  },
  computed:{
    ...mapGetters({
      user:'user',
      posts:'posts',
      status:'status',
      FriendButtontext:'FriendButtontext',
    })
  }
};
</script>

<style scoped>
</style>