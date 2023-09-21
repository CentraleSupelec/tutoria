import React from 'react';
interface GeneralErrorsRendererProps {
    errors: string[]
}

const GeneralErrorsRenderer = ({errors}: GeneralErrorsRendererProps) =>
    <>
        {errors.length? <ul className='form-general-errors mt-2 alert alert-danger'>
            {
                errors.map((errorMessage, index) => 
                    <li className='ms-2' key={`form-error-${index}`}>
                        {errorMessage}
                    </li>
                )
            }
        </ul> : null}
    </>;

export default GeneralErrorsRenderer;
