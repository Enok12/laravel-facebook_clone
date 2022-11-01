<template>
  <div>
    <img
      :src="imageObject.data.attributes.path"
      :alt="alt"
      ref="userImage"
      :class="classes"
    />
  </div>
</template>

<script>
import Dropzone from "dropzone";

export default {
  name: "UploadableImage",

  props: [
    'imageWidth',
    'imageHeight',
    'location',
    'userImage',
    'classes',
    'alt'
  ],

  data: () => {
    return {
      dropzone: null,
      uploadedImage:null,
    };
  },
  mounted() {
    this.dropzone = new Dropzone(this.$refs.userImage, this.settings);
    // console.log(this.imageObject.data.attributes.path);
  },
  computed: {
    settings() {
    let imageWidth = this.imageWidth;
    let imageHeight = this.imageHeight;
    let location = this.location;

      return {
        paramName: "image",
        url: "/api/user-images",
        acceptedFiles: "image/*",
        
        params: function params(files, xhr, chunk) { return { 'width' : imageWidth,'height': imageHeight,'location': location,
         }; },

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
          this.uploadedImage = res
          alert("Uploaded");
        },
      };
    },

    imageObject(){
      return this.uploadedImage || this.userImage
    }
  },
};
</script>

<style scoped>
</style>