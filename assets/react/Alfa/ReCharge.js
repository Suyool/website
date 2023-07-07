import React, { useEffect, useState } from "react";

const ReCharge = ({ activeButton, setActiveButton, setHeaderTitle,setBackLink }) => {
  useEffect(() => {
    setHeaderTitle("Re-charge Alfa")
    setBackLink("")
  }, [])

  return (
    <div id="ReCharge">
      ReCharge
    </div>
  );
};

export default ReCharge;
