<div class="activity__title">
  Activity Log
  <button class="activity__pin" 
          type="button"
          title="Pin activity panel" 
          aria-label="Pin activity panel"
          onclick="this.classList.toggle('activity__pin--active')">
    📌
  </button>
</div>
<div class="activity__content">
  <ul role="list">
    <li>Loaded page: {{ request()->path() }}</li>
    <li>ENV DEV_AUTO_LOGIN: {{ env('DEV_AUTO_LOGIN') ? 'ON' : 'OFF' }}</li>
    <li>Time: {{ now()->toDateTimeString() }}</li>
    <li>User Agent: {{ request()->userAgent() }}</li>
    <li>IP Address: {{ request()->ip() }}</li>
  </ul>
  <p class="activity__note">
    <small>Panel hides on tablets and mobile devices</small>
  </p>
</div>
