<template>
  <div id="page">
    <div id="register-wrapper" v-loading="loading">
      <form action="" id="login-form">
        <div class="weui-cells weui-cells_form">
          <div class="weui-cell description">
            <div class="weui-cell__bd" v-if="!sms_success" id="unsent-msg">
              <p>请输入手机号
                <br /> 验证码将通过短信发送到您的手机
              </p>
              <p class="sms-tips">(10分钟内，最多可以发送3次验证码)</p>
            </div>
            <div class="weui-cell__bd" v-else id="success-msg">
              <p>我们已将6位数验证码发送至您的手机，
                <br /> 验证码有效期为30分钟。
              </p>
              <p class="sms-tips">(如果您没有收到验证码，请10分钟后再试。)</p>
            </div>
          </div>
          <div class="weui-cell weui-cell_primary">
            <div class="weui-cell__bd">
              <input @blur="checkInput()" class="weui-input" type="tel" placeholder="手机号" v-model="sms_info.phone" id="sms-phonenumber">
            </div>
          </div>
          <div class="weui-cell weui-cell_vcode">
            <div class="weui-cell__bd">
              <input class="weui-input" type="tel" placeholder="输入验证码" v-model="sms_info.code" id="verify-input">
            </div>
            <div class="weui-cell__ft" v-if="!sent">
              <a class="weui-vcode-btn" @click.prevent="sendsms()" id="send_sms_btn">发送验证码</a>
            </div>
            <div class="weui-cell__ft" v-if="sent">
              <a class="weui-vcode-btn btn-resend" id="send_sms_btn">重新发送验证码
              </a>
            </div>
          </div>
          <div class="weui-btn-area">
            <a class="weui-btn weui-btn_primary" id="conf" @click="submit()">确认</a>
          </div>
        </div>
        <we-dialog :show="dialogVisible" :message=dialogMsg>
          <a v-if="dialogType === 'warning'" href="javascript:;" class="weui-btn weui-btn_mini weui-btn_primary" slot="footer" @click="dialogVisible=false" id="close-dialog">确定</a>
          <a v-if="dialogType == 'confirm'" class="weui-btn weui-btn_mini weui-btn_primary" slot="footer" @click="dialogVisible=false" id="close-dialog-no">否</a>
        </we-dialog>
      </form>
    </div>
  </div>


</template>

<script type="es6">
  import {Utils} from '../../store/util'

  export default {
    data() {
      return {
        loading: false,
        sms_info: {
          phone: '',
          code: ''
        },
        checkPhone: false,
        dialogType: 'warning',
        dialogVisible: false,
        dialogAction: '',
        dialogMsg: '',
        sent: false,
        sms_success: false
      }
    },
    mounted() {
      // keepalive情况下mount只进行一次
    },
    activated() {
      this.$store.commit('SET_BEARER', {token: null, exp: null});
      this.sms_info = {
        phone: '',
        code: '',
      };
    },
    methods: {
      checkInput(){
        this.checkPhone = false;
        let reg = /^0?(1[0-9])[0-9]{9}$/;
        if(this.sms_info.phone == ''){
          this.dialogType = 'warning';
          this.dialogMsg = '请输入手机号码';
          this.dialogVisible = true;
        }else{
          if(reg.test(this.sms_info.phone)){
            this.checkPhone = true;
          }else{
            this.dialogType = 'warning';
            this.dialogMsg = '请检查手机号格式是否正确，<br/>目前仅支持中国号码<br/>（11位数字）。';
            this.dialogVisible = true;
          }
        }
      },
      sendsms() {
        this.checkInput();
        if(this.checkPhone){
          let utils = new Utils();
          this.sent = true;
          this.$store.dispatch('sendSMS', this.sms_info).then(res => {
            this.sms_success = true;
            this.wxid = res.wxid;
            this.time = res.time;
            let vinpt = document.getElementById('verify-input');
            vinpt.focus();
          }).catch(response => {
            this.sent = false;
            if (response.status_code == 422){
              this.dialogType = 'warning';
              this.dialogMsg = '请检查手机号格式是否正确！';
              this.dialogVisible = true;
            }
            else if (response.status_code == 403) {
              this.dialogType = 'warning';
              this.dialogMsg = response.message;
              this.dialogVisible = true;
            }
            else {
              this.dialogType = 'warning';
              this.dialogMsg = response.message;
              this.dialogVisible = true;
            }
          });
        }
      },
      submit(tag) {
        this.dialogVisible = false;
        this.loading = true;
        this.$store.dispatch('getAuthToken', this.sms_info).then(res => {
          this.loading = false;
          this.$store.state.acpro.wx_functions.set_token(res.token);
          window.location.href = '/';
        }).catch(res => {
          this.loading = false;
          if (res.status_code == 403) {
            if (res.message === 'user_not_exists') {
              this.dialogType = 'confirm';
              this.dialogMsg = 'user not exits';
              this.dialogVisible = true;
            }
            else {
              this.dialogType = 'warning';
              this.dialogMsg = res.message;
              this.dialogVisible = true;
            }
          }
          else {
            this.dialogType = 'warning';
            this.dialogMsg = res.message;
            this.dialogVisible = true;
          }
        });
      }
    }
  }
</script>
