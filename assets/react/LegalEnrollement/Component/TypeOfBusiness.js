import React, { useState } from "react";

const TypeOfBusiness = ({
  getDropDown,
  setFormData,
  formData,
  handleInputChange,
}) => {
  const [selectedValue, setSelectedValue] = useState(false);

  const handleChange = (event) => {
    const { name, value } = event.target;
    console.log(value);

    if (value == "others") {
      setSelectedValue(true);
      setFormData((prevFormData) => ({
        ...prevFormData,
        [name]: "",
      }));
    } else {
      setFormData((prevFormData) => ({
        ...prevFormData,
        [name]: value,
      }));
    }
  };

  const handleRemove = () => {
    setFormData((prevFormData) => ({
      ...prevFormData,
      ["businessType"]: "",
    }));
    setSelectedValue(false);
  };

  return (
    <div className="dropdown-input">
      {selectedValue ? (
        <div className="others">
          <input
            className="input"
            placeholder="Others"
            name="businessType"
            value={formData["businessType"]}
            onChange={handleInputChange}
          />
          <div className="remove" onClick={handleRemove}>
            {" "}
            <img
              className="addImg"
              src="/build/images/removeName.png"
              alt="remove"
            />
          </div>
        </div>
      ) : (
        <select
          className="input"
          name="businessType"
          value={formData["businessType"]}
          onChange={handleChange}
        >
          <option value="0">Select value</option>
          {getDropDown.map((item, index) => (
            <option key={index} value={item}>
              {item}
            </option>
          ))}
          <option value="others">Others</option>
        </select>
      )}
    </div>
  );
};

export default TypeOfBusiness;
