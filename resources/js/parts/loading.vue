<template>
  <div>
    <div class="loading-mask" :style="disp_style">
      <div class="loading-spinner">
        <svg viewBox="25 25 50 50" class="circular"><circle cx="50" cy="50" r="20" fill="none" class="path"></circle>
        </svg><p class="loading-text"><slot></slot></p>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .loading-mask {
    position: fixed;
    z-index: 10000;
    background-color: hsla(0,0%,100%,.9);
    height: 100%;
    margin: 0;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    transition: opacity .5s;
  }

  .loading-spinner {
    top: 36%;
    margin-top: -21px;
    width: 100%;
    text-align: center;
    position: absolute;
  }

  .loading-spinner .circular {
    width: 42px;
    height: 42px;
    animation: loading-rotate 2s linear infinite;
  }

  .loading-spinner .path {
    animation: loading-dash 1.5s cubic-bezier(0.47, 0, 0.75, 0.72) infinite;
    stroke-dasharray: 90,150;
    stroke-dashoffset: 0;
    stroke-width: 2;
    stroke: #20a0ff;
    stroke-linecap: round;
  }
  .loading-spinner .loading-text {
    color: #20a0ff;
    margin: 3px 0;
    font-size: 14px;
  }

  .circular {
    width: 42px;
    height: 42px;
    animation: loading-rotate 2s linear infinite
  }

  @keyframes loading-dash {
    0% {
      stroke-dasharray: 1, 200;
      stroke-dashoffset: 0
    }
    50% {
      stroke-dasharray: 90, 150;
      stroke-dashoffset: -40px
    }
    to {
      stroke-dasharray: 90, 150;
      stroke-dashoffset: -120px
    }
  }

  @keyframes loading-rotate {
    to {
      transform: rotate(1turn)
    }
  }
</style>

<script>
  export default {
    props: {
      disp: {
        default: false
      }
    },
    data () {
      return {
        disp_style: {
          display: 'none',
          opacity: 0
        },
        tid: false
      }
    },
    mounted () {
      // this.ctl_disp(this.disp);
    },
    methods: {
      ctl_disp (val) {
        if (this.tid) {
          clearTimeout(this.tid);
        }
        if (val) {
          // 先改了block再变成1了
          this.disp_style.display = 'block';
          this.tid = setTimeout(() => {
            this.disp_style.opacity = 1;
            this.tid = false;
          }, 300);
        } else {
          this.disp_style.opacity = 0;
          this.tid = setTimeout(() => {
            this.disp_style.display = 'none';
            this.tid = false;
          }, 300);
        }
      }
    },
    watch: {
      'disp': 'ctl_disp'
    }
  }
</script>
