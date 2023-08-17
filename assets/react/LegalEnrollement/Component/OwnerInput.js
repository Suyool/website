import React from 'react';

function OwnerInput({ data, setData }) {


    function handleAdd() {
        setData([...data, { Name: '' }]);
    }

    function handleRemove(index) {
        setData(data.filter((owner, i) => i !== index));
    }


    function handleInputChange(event, index, key) {
        const { value } = event.target;
        setData(
            data.map((row, i) =>
                i === index ? { ...row, [key]: value } : row
            )
        );
    }


    return (
        <div className='row'>
            <div className="employees-input">
                <div className="label">Mention any person or legal entity that has more than 25% shares in the company</div>

                {data.length !== 0 && data.map((item, index) => (
                    <div className="row pt-2" key={index}>
                        <div className='col-lg-4 col-md-6 col-10'>
                            <input
                                className="input"
                                placeholder="Name"
                                value={item.Name}
                                onChange={(event) => handleInputChange(event, index, 'Name')}
                            />
                        </div>
                        <div className='col-lg-4 col-md-6 col-2 '>
                            <button className='removeTill' onClick={() => handleRemove(index)}>
                                <img src="/build/images/removeName.png" />
                            </button>
                        </div>
                    </div>
                ))}

                <div className='row'>
                    <div className="col-lg-4 col-md-6 col-sm-12">
                        <div className="row component">
                            <div className="col-12 subTitle">
                                <button className='addTill' onClick={handleAdd}>
                                    + ADD ANOTHER EMPLOYEE
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    );
}

export default OwnerInput;
