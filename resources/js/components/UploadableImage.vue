<template>
  <div>
    <img
      :src="userImage.data.attributes.path"
      :alt="alt"
      ref="userImage"
      :class="classes"
    />
  </div>
</template>

<script>
import Dropzone from "dropzone";
import { mapGetters } from "vuex";

export default {
  name: "UploadableImage",

  props: [
    "imageWidth",
    "imageHeight",
    "location",
    "userImage",
    "classes",
    "alt",
  ],

  data: () => {
    return {
      dropzone: null,
    };
  },
  mounted() {
    if (this.authUser.data.user_id == this.$route.params.userId) {
      this.dropzone = new Dropzone(this.$refs.userImage, this.settings);
      console.log('okay');
    }

  },

  computed: {

   ...mapGetters({
      authUser:'authUser',
    }),

    settings() {
      let imageWidth = this.imageWidth;
      let imageHeight = this.imageHeight;
      let location = this.location;

      return {
        paramName: "image",
        url: "/api/user-images",
        acceptedFiles: "image/*",

        params: function params(files, xhr, chunk) {
          return { width: imageWidth, height: imageHeight, location: location };
        },

        // params:{
        //     'width': this.imageWidth,
        //     'height': this.imageHeight,
        //     'location': this.location,
        // },
        headers: {
          "X-CSRF-TOKEN": document.head.querySelector("meta[name=csrf-token]")
            .content,
        },
        success: (e, res) => {
          this.$store.dispatch("fetchAuthUser");
          this.$store.dispatch("fetchUser", this.$route.params.userId);
          this.$store.dispatch("fetchUserPosts", this.$route.params.userId);
        },
      };
    },
  },
};
</script>

<style scoped>
</style>