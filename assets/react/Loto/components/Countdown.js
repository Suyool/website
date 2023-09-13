import React, { useEffect, useState } from "react";

const Countdown = ({ nextDrawNumber }) => {
  const targetDate = new Date(nextDrawNumber).getTime();

  const [ days, setDays ] = useState(0);
  const [ hours, setHours ] = useState(0);
  const [ minutes, setMinutes ] = useState(0);
  const [ seconds, setSeconds ] = useState(0);

  useEffect(() => {
    const interval = setInterval(() => {
      const now = new Date().getTime();
      const nowUtc =
        new Date(now).getTime() + new Date().getTimezoneOffset() * 60000;
      const targetDateUtc = targetDate + new Date().getTimezoneOffset() * 60000;
      const remainingTime = targetDateUtc - nowUtc;

      if (remainingTime > 0) {
        const oneDay = 24 * 60 * 60 * 1000;
        const oneHour = 60 * 60 * 1000;
        const oneMinute = 60 * 1000;

        const days = Math.floor(remainingTime / oneDay);
        const hours = Math.floor((remainingTime % oneDay) / oneHour);
        const minutes = Math.floor((remainingTime % oneHour) / oneMinute);
        const seconds = Math.floor((remainingTime % oneMinute) / 1000);

        setDays(days);
        setHours(hours);
        setMinutes(minutes);
        setSeconds(seconds);
      } else {
        clearInterval(interval);
      }
    }, 1000);

    return () => {
      clearInterval(interval);
    };
  }, []);

  return (
    <div className="timeSection">
      <div className="items">
        <div className="number">{days.toString().padStart(2, "0")}</div>
        <div className="date">DAYS</div>
      </div>
      <div className="items">
        <div className="number">{hours.toString().padStart(2, "0")}</div>
        <div className="date">HOURS</div>
      </div>
      <div className="items">
        <div className="number">{minutes.toString().padStart(2, "0")}</div>
        <div className="date">MIN</div>
      </div>
      <div className="items">
        <div className="number">{seconds.toString().padStart(2, "0")}</div>
        <div className="date">SEC</div>
      </div>
    </div>
  );
};

export default Countdown;
