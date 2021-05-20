<template>
  <mdb-carousel
    :interval="3000"
    slide
    :items="items"
    indicators
  ></mdb-carousel>
</template>

<script>
  import { mdbCarousel } from "mdbvue";
  export default {
    name: "CarouselPage",
    components: {
      mdbCarousel
    },
    props: {
    items: {
      type: Array,
      default: [],
    },
  },
    data() {
      return {
        carousel: 2,
      };
    }
  };
</script>