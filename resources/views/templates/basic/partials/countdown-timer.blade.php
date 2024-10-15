 <div class="flip-clock" data-start-at="{{ $item->stream->start_at }}">
     <div class="flip-clock-item">
         <span class="flip-clock-number days">00</span>
         <span class="flip-clock-label">@lang('DAYS')</span>
     </div>
     <div class="flip-clock-item">
         <span class="flip-clock-number hours">00</span>
         <span class="flip-clock-label">@lang('HOURS')</span>
     </div>
     <div class="flip-clock-item">
         <span class="flip-clock-number minutes">00</span>
         <span class="flip-clock-label">@lang('MINUTES')</span>
     </div>
     <div class="flip-clock-item">
         <span class="flip-clock-number seconds">00</span>
         <span class="flip-clock-label">@lang('SECONDS')</span>
     </div>
 </div>


 @push('style')
     <style>
         .flip-clock {
             display: flex;
             justify-content: center;
             align-items: center;
             position: absolute;
             bottom: 20px;
             left: 50%;
             transform: translateX(-50%);
             background-color: rgba(0, 0, 0, 0.5);
             border-radius: 10px;
             padding: 10px 20px;
         }

         .flip-clock-item {
             margin: 0 5px;
             text-align: center;
         }

         .flip-clock-number {
             display: block;
             font-size: 3rem;
             color: #fff;
             background-color: #333;
             border-radius: 5px;
             padding: 10px 20px;
             box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
         }

         .flip-clock-label {
             display: block;
             font-size: 0.8rem;
             color: #fff;
             margin-top: 5px;
         }

         @media (max-width: 768px) {
             .flip-clock-number {
                 font-size: 2rem;
                 padding: 5px 10px;
             }

             .flip-clock-label {
                 font-size: 0.7rem;
             }
         }
     </style>
 @endpush

 @push('script')
     <script>
         document.addEventListener('DOMContentLoaded', function() {
             var startAt = document.querySelector('.flip-clock').getAttribute('data-start-at');
             var daysElement = document.querySelector('.flip-clock .days');
             var hoursElement = document.querySelector('.flip-clock .hours');
             var minutesElement = document.querySelector('.flip-clock .minutes');
             var secondsElement = document.querySelector('.flip-clock .seconds');

             function updateCountdown() {
                 var now = new Date().getTime();
                 var eventTime = new Date(startAt).getTime();
                 var distance = eventTime - now;

                 if (distance <= 0) {
                     // Stream started, hide the countdown
                     document.querySelector('.flip-clock').style.display = 'none';
                     return;
                 }

                 var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                 var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                 var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                 var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                 daysElement.innerHTML = days < 10 ? '0' + days : days;
                 hoursElement.innerHTML = hours < 10 ? '0' + hours : hours;
                 minutesElement.innerHTML = minutes < 10 ? '0' + minutes : minutes;
                 secondsElement.innerHTML = seconds < 10 ? '0' + seconds : seconds;

                 setTimeout(updateCountdown, 1000);
             }

             // Start the countdown
             updateCountdown();
         });
     </script>
 @endpush
