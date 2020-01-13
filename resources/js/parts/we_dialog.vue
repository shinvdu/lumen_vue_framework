<template>
  <div id="dialog-wrapper" class="dialog" :style="{'display': display }">
    <div class="weui-mask"></div>
    <div class="weui-dialog">
      <div class="dialog-icon" :class="[className]"></div>
      <div class="weui-dialog__bd" id="dialog-msg" v-html="message" v-if="message">
        
      </div>
      <div v-html="action_dialogMsg" @click="actionFun" class="weui-dialog__bd" v-if="action_dialogMsg">

      </div>

        <div class="weui-dialog__ft">
          <slot name="footer">

          </slot>
        </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: ['show', 'type', 'message', 'action_dialogMsg'],
    data () {
      return {
        display: 'none',
        className: 'warning',
      }
    },
    mounted () {
      this.disp(this.show);
      this.initType(this.type);
    },
    methods: {
      disp (val) {
        if (val) {
          this.display = 'block';
        } else {
          this.display = 'none';
        }
      },
      initType (type) {
        this.className = type || 'warning';
      },
      actionFun () {
        this.$emit('action', {});
      }
    },
    watch: {
      'show': function (newval, oldval) {
        this.disp(newval);
      },
      'type': function (newval) {
        this.initType(newval);
      }
    }
  }
</script>
