/* eslint-disable no-unused-vars */
import React from 'react'
/* eslint-enable no-unused-vars */
import Base from "~/common/components/Base";
import {nl2br} from "~/util/element";

export default class Complete extends Base {
  constructor(props) {
    super(props);
  }

  render() {
    return (
      <section className="panel payment payment-thanks">
        <div className="panel-container">
          <h3>{__('Thank You')}</h3>
          <p>{__("Your transaction and registration to the payment plan was successful.")}</p>
          <p>{__("In the case of invoice payment, we conduct a credit check. As a result of the investigation, we will contact you if we deem it impossible to trade.")}</p>
          <a className="" href="/payments">{__('Move to Billing page')}</a>
        </div>
        <div className="confetti-cannon">
        </div>
      </section>
    )
  }

  componentDidMount(){
    var confettiCannon = document.getElementsByClassName('confetti-cannon')[0];
    if(confettiCannon){
      setTimeout(function(){
        for(i=0;i<(confettiCannon.clientWidth);i++){
          setTimeout(function(){
            var confetti = document.createElement("DIV");
            confetti.classList.add('confetti');
            confetti.classList.add('confetti-'+parseInt((Math.round(Math.random()*3))+1));
            confetti.style.left=(Math.round(Math.random()*100))+'vw';
            confettiCannon.appendChild(confetti);
          }, (Math.round(Math.random()*1))+(i*5)+(320/confettiCannon.clientWidth));
        }
      },1000);
    }
  }
}
