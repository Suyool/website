import React from "react";

const LegalForm = ({ getDropDown, setFormData, formData }) => {

    const handleChange = (event) => {
        const { name, value } = event.target;
        console.log(value)

        setFormData((prevFormData) => ({
            ...prevFormData,
            [name]: value,
        }));
    };

    return (
        <div className="col-lg-4 col-md-6 col-sm-12">
            <div className="label">Drop down</div>
            <div className="dropdown-input">
                <select
                    className="input"
                    name="legalForm"
                    value={formData["legalForm"]}
                    onChange={handleChange}
                >
                    <option value="0">Select value</option>
                    {getDropDown.map((item, index) => (
                        <option key={index} value={item}>
                            {item}
                        </option>
                    ))}
                </select>
            </div>
        </div>
    );
};

export default LegalForm;
