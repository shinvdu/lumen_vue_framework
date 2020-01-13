<template>
  <div>

  </div>
</template>

<script type="es6">
  import {Utils} from '../../store/util'
  export default {
    data() {
      return {
        scan_code:''
      }
    },
    mounted() {
      console.log('createdlogout');
      this.$store.state.acpro.wx_functions.logout();
      // window.location.reload(true);
    }
  }
</script>
